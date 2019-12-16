@extends('adminlte::page')

@section('title', 'Programas')

@section('content')

<!-- MOSTRA MENSAGEM -->
<?php echo simpleMessage() ?>


<!-- LINHA TITULO, PESQUISA/BUSCA E NOVO REGISTRO -->
<form action="/programs" method="get">

  <?php echo simpleHeadTable(route('programs.create')); ?>

  <!-- CAMPOS PARA FILTRAGEM -->
  <div class="collapse" id="filtros">
    <div class="card card-body">

      <div class="row">

        <!-- FILTRO - PRIMEIRA COLUNA -->
        <div class="col">
          <!-- FILTRO POR NOME -->
          <div class="form-group row">
            <label for="name" class="col-sm-3 col-form-label">Nome</label>
            <div class="col-sm-9">
              <input class="form-control form-control-sm" id="name" name="name" value="{{session('name')}}" placeholder="Nome">
            </div>
          </div>
          <!-- FILTRO POR ROTA -->
          <div class="form-group row">
            <label for="route" class="col-sm-3 col-form-label">E-Mail</label>
            <div class="col-sm-9">
              <input class="form-control form-control-sm" id="route" name="route" value="{{session('route')}}" placeholder="Rota">
            </div>
          </div>
        </div>

      </div>
        <!-- BOTÂO APLICAR FILTRAR -->
        <?php echo simpleApplyFilters() ?>

    </div>
  </div>
  <!-- FIM CAMPOS PARA FILTRAGEM -->


  <!-- TABELA PRINCIPAL DE REGISTROS -->
  <div class="row">
    <div class="col-12">

      <div class="card">
        <div class="card-body">

          <table class="bg-white table table-striped table-hover nowrap rounded table-striped table-sm" cellspacing="0">
            <thead>
              <tr>
                <th> <?php echo simpleColumn('id', 'ID') ?></th>
                <th> <?php echo simpleColumn('name', 'NOME') ?></th>
                <th> <?php echo simpleColumn('route', 'ROTA') ?></th>
                <th> <?php echo simpleColumn('icon', 'ICONE') ?></th>
                <th> <?php echo simpleColumn('show_menu', 'MOSTRAR NO MENU') ?></th>
                <th> <?php echo simpleColumn('created_at', 'CRIAÇÂO') ?></th>
                <th> AÇÔES </th>
              </tr>
            </thead>

            <tbody>
              @foreach($programs as $program)
              <tr>
                <td>{{$program->id}}</td>
                <td>{{$program->name}}</td>
                <td>{{$program->route}}</td>
                <td>{{$program->icon}}</td>
                <td>{{$program->show_menu}}</td>
                <td>{{simpleDateFormat($program->created_at)}}</td>
                <!-- BOTÕES DE AÇÃO -->
                <td>
                  <?php echo simpleAction('EDITAR', 'programs.edit', 'info', 'fa-edit', $program->id); ?>
                </td>
              </tr>
              @endforeach
              </body>

              <!-- TFOOT - IMPORTANTE PARA MELHORAR VISUAL -->
            <tfoot>
              <tr>
                <th colspan="100"></th>
              </tr>
            </tfoot>
          </table>

          <!-- RODAPE NAVEGADOR DE PAGINAS -->
          <?php echo simpleFootTable($programs) ?>
          <!-- FIM - RODAPE -->

        </div>
      </div>
    </div>
  </div>
</form>
@stop