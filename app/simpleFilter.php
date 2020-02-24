<?php
/*
|--------------------------------------------------------------------------
| Simple Filter - By Helder - 05-12-2019
|--------------------------------------------------------------------------
|
| Functions to help show table and control pagination with filters
|
*/

#use Illuminate\Support\Facades\Redirect;
#use Intervention\Image\Image; NAO PODE DESCOMENTAR

/**
 * Slugfy 
 * @param  string $name
 * 
 * @return string $slug
 */

function simpleSlugify($name)
{
  /*
  $slug = substr(mb_strtolower($name),0,40);
  $slug = str_replace(' ', '_', $slug);
  $slug = str_replace('.', '_', $slug);
  $slug = str_replace(',', '_', $slug);
  $slug = str_replace("\t", '_', $slug);
  return $slug;
  */
  // Slugs não podem conter caracteres especiais ( somente letras e numeros )
  $slug = mb_strtolower(str_replace('/', ' ', preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim($name)))));
  // Devem usar - ao inves de _ ( padrão google de busca, seguido pela IFC )
  return (str_replace(" ", "-", $slug));
}
/**
 * Validade and save image to storage
 *
 * @param  string  $field    Name of field in the HTML Form
 * @param  string  $slug     Name of file
 * @param  string  $path     Path to save the file in the /storage/app/public/$path
 * @param  string  $desktop  Array of size with and height
 * @param  string  $mobile   Array of size with and height
 * @param  string  $shortcut Array of size with and height
 * 
 * @return string
 */
function simpleUpload(
  $field,
  $slug,
  $path,
  array $desktop = [],
  array $mobile = [],
  array $shortcut = []
) {

  // Imagens em tamanho sdiferentes para resoluções diferentes
  $dimensions = [];

  if (!empty($desktop)) {
    $dimensions['desktop'] =
      [
        'with' => $desktop[0],
        'height' => $desktop[1]
      ];
  }

  if (!empty($mobile)) {
    $dimensions['mobile'] =
      [
        'with' => $mobile[0],
        'height' => $mobile[1]
      ];
  }
  if (!empty($shortcut)) {
    $dimensions['thumbnail'] =
      [
        'with' => $shortcut[0],
        'height' => $shortcut[1]
      ];
  }

  if (empty($dimensions)) {
    return '';
  }

  // Analisa upload de imagens
  if (request()->hasFile($field)) {

    // Caso haja a tentativa de um upload
    $name = '';

    // Obtem os dados da imagem
    $file = request()->file($field);
    $extension = $file->extension();
    $mimeType = $file->getMimeType();

    $extensions = ['image/png', 'image/jpeg', 'image/jpg'];

    if (!in_array($mimeType, $extensions)) {
      throw \Illuminate\Validation\ValidationException::withMessages([$field => ['Tipo de arquivo não permitido. Somente imagens: .jpg ou .png']]);
    }

    // open an image file
    $image = Image::make($file);
    // Imagem otimizada  com performance de até 5 vzs melhor
    $webp = Image::make($file)->encode('webp');

    // Link do storage
    $storage = storage_path();

    // Para cada dimensão
    foreach ($dimensions as $dir => $dimension) {

      // Redimensiona a imagem
      $image->resize($dimension['with'], $dimension['height']);
      $webp->resize($dimension['with'], $dimension['height']);

      // Nome da imagem ( slug + extension )
      $name = "{$slug}.{$extension}";
      $nameWebp = "{$slug}.webp";

      // Diretório do upload
      $filename = "{$storage}/app/public/{$path}/{$dir}/{$name}";
      // Imagem otimizada
      $filenameWebp = "{$storage}/app/public/{$path}/{$dir}/{$nameWebp}";

      if (!file_exists("{$storage}/app/public/{$path}/{$dir}")) {
        mkdir("{$storage}/app/public/{$path}/{$dir}");
      }

      if (file_exists($filename)) {
        // Se já existir uma imagem com mesmo nome/dimensão, exclui
        unlink($filename);
      }
      // Atualiza
      $upload = $image->save($filename, 70);

      if (!$upload)
        throw \Illuminate\Validation\ValidationException::withMessages(['image' => ['Erro ao gravar imagem']]);

      if (file_exists($filenameWebp)) {
        // Se já existir uma imagem com mesmo nome/dimensão, exclui
        unlink($filenameWebp);
      }
      // Atualiza
      $upload = $image->save($filenameWebp, 70);

      if (!$upload)
        throw \Illuminate\Validation\ValidationException::withMessages(['image' => ['Erro ao gravar imagem']]);
    }
  }
  return $name;
}


/**
 * Return HTML for forma head with ID or NEW
 *
 * @param  string  $title
 * @param  string  $id
 * 
 * @return string
 */
function simpleFormHead($id)
{
  $status = $id ? 'ID: ' . $id : 'Novo';
  $program = session('program');
  $icon = session('icon');
  $selected_name = session('selected_name') ? "- " . session('selected_name') : '';

  $html = "
    <div class='row'>
      <div class='col-9'>
        <h4> <i class='fa fa-$icon'></i> &nbsp; $program <b>$selected_name</b> </h4>
      </div>
      <div class='col-3 float-left d-flex justify-content-end'>
        <p>$status</p>
      </div>
    </div>";
  return $html;
}


/**
 * Return HTML for form to submit and return to table
 *
 * @return string
 */
function simpleFormButtons($route = '')
{
  $buttom = $route ? "<a href='$route' class='btn btn-secondary btn-sm'>Voltar a listagem</a>" : '';
  $html = "  
  <div class='row'>
    <div class='col-sm-12'>
      <button type='submit' class='btn btn-dark btn-sm'>Salvar</button>
      $buttom
    </div>
  </div>";
  return $html;
}

/**
 * Return HTML for messages, sucesses and errors
 *
 * @return string
 */
function simpleMessage($errors = '')
{
  $html = '';

  $erros = '';
  if ($errors && $errors->any()) {
    foreach ($errors->all() as $error) {
      $erros .= "<li> $error </li>";
    }
  }

  $mensagens['danger'] = $erros;
  $mensagens['warning'] = session('message');
  $mensagens['success'] = session('success');

  foreach ($mensagens as $cor => $mensagem) {
    if ($mensagem) {
      $html .= "
        <div class='alert alert-$cor alert-dismissible fade show' role='alert'>
          <strong>Atenção:</strong> $mensagem
          <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
            <span aria-hidden='true'>&times;</span>
          </button>
        </div>
    ";
    }
  }
  return $html;
}


/**
 * Return HTML for column head of table lines
 *
 * @param  string  $column
 * @param  string  $description
 * 
 * @return string
 */

function simpleColumn($column, $description)
{
  $route = request()->path();
  #dd($route);
  $order = session('order', 'asc') == 'asc' ?  'down' : 'up';
  $icone = session('column') == $column ? "<i class='fa fa-caret-$order'> </i>" : '';
  $html = "<a href='$route?column=$column'> $icone $description</a>"; #class='badge badge-dark'
  return $html;
}

/**
 * Formata data e hora d-m-Y H:i
 *
 * @param $date_time
 * 
 * @return string
 */
function simpleDateFormat($date_time)
{
  if (empty($date_time)) return '';
  try {
    $date = new DateTime($date_time);
    return \Carbon\Carbon::parse($date)->format('d-m-Y H:i');
  } catch (Exception $e) {
    return '';
  }
}


/**
 * Return HTML for button to apply filters
 *
 * @return string
 */
function simpleApplyFilters()
{
  return "
  <div class='row'>
  <div class='col-12 d-flex justify-content-end'>
    <div class='form-group row'>
      <button type='submit' class='btn btn-secondary'>Aplicar Filtros</button>
    </div>
  </div>
</div>";
}

/**
 * Return HTML for action 
 *
 * @param  string  $title
 * @param  string  $button
 * @param  string  $route
 * @param  string  $icon
 * @param  string  $id
 * 
 * @return string
 */
function simpleAction($title, $route, $color, $icon = 'fa-edit', $id = null)
{
  /*
  Colors:
  primary     econdary    success 
  danger       warning    info
  light        dark       white
  */
  $color = $color ?? 'info';
  $rota = $id ? route($route, $id) : route($route);
  $html = " <a href='$rota' title='$title' role='button' class='btn btn-sm btn-spinner btn-$color'>
            <i class='fa $icon'></i>
            </a> ";
  return $html;
}


function simpleMenu($grupo_program, $icon, $submenus)
{
  $icon = $icon ?? 'cog';
  return ['text' => $grupo_program, 'icon' => 'fas fa-fw fa-' . $icon, 'submenu' => $submenus];
}

function simpleSubmenu($grupo_program, $icon, $rota)
{
  $icon = $icon ?? 'cog';
  return ['text' => $grupo_program, 'icon' => 'fas fa-fw fa-' . $icon, 'url' => $rota, 'active' => [$rota, $rota . '/*', "regex:@^$rota\?*@"]];
}

/**
 * Store seseeion and return number os records, ordered column and direction
 *
 * @param  string  $column
 * 
 * @return mixed
 */
function simpleParameters($default_column, $order = 'desc')
{
  $controller = request()->path();
  $atual = session('controller');
  // Faz limpeza parametros filtros quando muda de programa
  if ($controller != $atual) {
    $programs = session('programs', []);
    $icons = session('icons', []);
    $program = substr($controller, 0, 100);
    #var_dump($programs); var_dump(session()->all()); dd($program);
    if (!isset($programs[$program])) {
      abort(redirect('/home')); // DESATIVAR AQUI PARA PERMITIR ACESSAR TODOS PROGRAMAS
      #dd('SEM PERMISSÂO'); 
    }
    // Limpeza sessão
    session(['filters' => array()]);
    session(['descriptions' => []]);
    // Armazena Parametros 
    session(['controller' => $controller]);
    session(['column' => $default_column]);
    session(['order' => $order]);
    session(['back' => session('route_back')]);
    session(['icon' => $icons[$program]]);
    session(['program' => $programs[$program]]);
    session(['selected_id' => session('id')]);
    session(['selected_name' => session('name')]);
    #var_dump(session()->all()); exit;
  }

  $records = request('records', session('records', 5));
  $column = request('column', session('column')) ?? $default_column;
  session(['order' => session('order', 'desc')]);
  if (request('column')) {
    if ($column == session('column')) {
      $order = (session('order') == 'desc') ? 'asc' : 'desc';
      session(['order' => $order]);
    }
  }

  session(['records' => $records]);
  session(['column' => $column]);

  return array($records, $column, session('order'));
}

/**
 * Store session and return filtered field
 *
 * @param  string  $field
 * @param  string  $description
 * 
 * @return mixed
 */
function simpleFilter($field, $description = '')
{
  $filters = session('filters', []);
  if (isset($_REQUEST[$field])) {
    $value = request($field);
    $filters[$field] = $value;
    session(['filters' => $filters]);
    // Para trazer no cabeçalho da tabela
    $descriptions = session('descriptions');
    $descriptions[$description] = $value;
    session(['descriptions' => $descriptions]);
  }
  return $filters[$field] ?? '';
}

/**
 * Return head of table with filter button and new record button
 *
 * @param  string  $create_new
 * 
 * @return string
 */
function simpleHeadTable($create_new = true, $extra_button = [])
{
  $icon = session('icon');
  $program = session('program');
  $selected_name = session('selected_name') ? "- " . session('selected_name') : '';

  // Pega os filtro atuais
  $filters = '';
  $descriptions = session('descriptions');
  if ($descriptions) {
    #var_dump($descriptions);    exit;
    foreach ($descriptions as $description => $value) {
      if ($description && $value) {
        $filters .= $description . ': ' . $value . ', ';
      }
    }
  }
  $filters = empty($filters) ? '' : substr($filters, 0, strlen($filters) - 2);

  // Include button create new ?
  $include_create_new = $create_new ?
    "
    <!-- BOTÃO PARA INCLUIR -->
    <a href='" . request()->path() . "/create' class='btn btn-dark' data-style='zoom-in'>
      <span class='ladda-label'> <i class='fa fa-plus'></i> Incluir</span>
    </a>
    " : '';
  $back_button = session('back');
  $include_back = $back_button ?
    "
    <!-- BOTÃO PARA VOLTAR -->
    <a href='$back_button' class='btn btn-dark' data-style='zoom-in'>
      <span class='ladda-label'> <i class='fa fa-angle-double-left'></i> Voltar</span>
    </a>
    " : '';

  $include_extra_button = !empty($extra_button) ?
    "
    <!-- BOTÃO EXTRA -->
    <a href='" . $extra_button[0] . "' class='btn btn-dark' data-style='zoom-in'>
      <span class='ladda-label'> <i class='fa fa-cog'></i> " . $extra_button[1] . "</span>
    </a>
    " : '';

  return "
    <div class='row'>

    <div class='col-5'>
      <h4> <i class='fa fa-$icon'></i> $program <b> $selected_name </b></h4>
    </div>
    
    <div class='col-4'>
      $filters
    </div>

    <div class='col-3 float-left d-flex justify-content-end'>
        <p>
          <!-- BOTÂO DE FILTRAR -->
          <button class='btn btn-secondary' type='button' data-toggle='collapse' data-target='#filtros' aria-expanded='false' aria-controls='filtros'>
            Filtrar
          </button>
          $include_create_new
          $include_back
          $include_extra_button
        </p>
      </div>
    </div>
    ";
}



/**
 * Return selected qith options
 *
 * @param  integer $id
 * @param  records $records
 * 
 * @return mixed
 */
function simpleSelect($field, $selected_id, $records)
{
  $selects = "<select name='$field' class='form-control form-control-sm' id='supplier'> <option value=''> </option> \n";
  #dd($records);
  $selected_id = old($field, $selected_id); # ID atual
  foreach ($records as $option => $id) {
    $selected = $selected_id == $id ? 'selected' : '';
    $selects .= "<option value='$id' $selected> $option </option> \n";
  }
  #dd($selects);
  return $selects . "</select> \n";
}

/**
 * Return footer of table with pagination and number of records options
 *
 * @param  string  $records
 * 
 * @return mixed
 */
function simpleFootTable($records)
{
  $html = "
  <div class='container-fluid shadow p-2 mb-3 bg-white rounded'>
  <div class='row d-flex align-items-center'>
    <div class='col-3'>
      Página {$records->currentPage()} de {$records->lastPage()}
    </div>
    <div class='col-7 d-flex justify-content-center align-items-center'>
      " . $records->onEachSide(5)->links() . "
    </div>

    <div class='col-2'>
      <div class='form-group row'>
        <label for='records' class='col-sm-5 col-form-label'>Registros:</label>
        <div class='col-sm-4'>
          <select id='records' name='records' class='form-control form-control-sm' onchange='this.form.submit()'>
          ";
  $options = array(5, 10, 20, 50, 100);
  $selects = "";
  foreach ($options as $option) {
    $selected = session('records') == $option ? 'selected' : '';
    $selects .= "<option value='$option' $selected>$option</option>";
  }
  $html = $html . $selects;
  $html .= "
          </select>
        </div>
      </div>
    </div>

  </div>
</div>
";
  return $html;
}
