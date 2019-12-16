@extends('adminlte::page')

@section('title', 'Grupo de Programas')

@section('content')

<!-- MOSTRA MENSAGEM -->
<?php echo simpleMessage() ?>


<!-- LINHA TITULO, PESQUISA/BUSCA E NOVO REGISTRO -->
<form action="/group-programs" method="get">

  <?php 
  $group = session('group');
  echo simpleHeadTable(); 
  ?>

  <!-- CAMPOS PARA FILTRAGEM -->
  <div class="collapse" id="filtros">
    <div class="card card-body">

      <div class="row">

        <!-- FILTRO - PRIMEIRA COLUNA -->
        <div class="col">
          <!-- FILTRO POR NOME -->
          <div class="form-group row">
            <label for="name" class="col-sm-2 col-form-label">Nome</label>
            <div class="col-sm-10">
              <input class="form-control form-control-sm" id="name" name="name" value="{{session('name')}}" placeholder="Nome">
            </div>
          </div>
          <!-- FILTRO POR EMAIL -->
          <div class="form-group row">
            <label for="email" class="col-sm-2 col-form-label">E-Mail</label>
            <div class="col-sm-10">
              <input class="form-control form-control-sm" id="email" name="email" value="{{session('email')}}" placeholder="E-Mail">
            </div>
          </div>
        </div>

        <!-- BOTÂO APLICAR FILTRAR -->
      </div>
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
                <th> <?php echo simpleColumn('program_name',  'USUÀRIO') ?></th>
                <th> <?php echo simpleColumn('route',  'ROTA') ?></th>
                <th> <?php echo simpleColumn('updated_at', 'ASSOCIAÇÂO') ?></th>
                <th> <?php echo simpleColumn('active',     'ATIVO') ?></th>
                <th> AÇÔES </th>
              </tr>
            </thead>

            <tbody>
              @foreach($groupPrograms as $group)
              <tr>
                <?php #var_dump($group); exit;?>
                <td>{{$group->id}}</td>
                <td>{{$group->program_name}}</td>
                <td>{{$group->route}}</td>
                <td>{{$group->active == 'S' ? simpleDateFormat($group->updated_at) : ''}}</td>
                <td>{{$group->active == 'S' ? $group->active : 'N'}}</td>
                <!-- BOTÕES DE AÇÃO -->
                <td>
                  <?php
                  echo simpleAction(
                    $group->active == 'S' ? 'Retirar' : 'Incluir',
                    'group-programs.show',
                    $group->active == 'S' ? 'danger' : 'success',
                    'fa-edit',
                    $group->id
                  );
                  ?>
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
          <?php echo simpleFootTable($groupPrograms) ?>
          <!-- FIM - RODAPE -->

        </div>
      </div>
    </div>
  </div>
</form>
@stop