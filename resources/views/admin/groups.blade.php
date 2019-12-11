@extends('adminlte::page')

@section('title', 'Groupas')

@section('content')

<!-- MOSTRA MENSAGEM -->
<?php echo simpleMessage() ?>


<!-- LINHA TITULO, PESQUISA/BUSCA E NOVO REGISTRO -->
<form action="/groups" method="get">

  <?php echo simpleHeadTable('Cadastro de Grupos', route('groups.create')); ?>

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
          <!-- FILTRO POR ROTA -->
          <div class="form-group row">
            <label for="route" class="col-sm-2 col-form-label">E-Mail</label>
            <div class="col-sm-10">
              <input class="form-control form-control-sm" id="route" name="route" value="{{session('route')}}" placeholder="Rota">
            </div>
          </div>
        </div>

        <!-- BOTÂO APLICAR FILTRAR -->
        <?php echo simpleApplyFilters() ?>
      </div>

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
                <th> <?php echo simpleColumn('created_at', 'CRIAÇÂO') ?></th>
                <th> AÇÔES </th>
              </tr>
            </thead>

            <tbody>
              @foreach($groups as $group)
              <tr>
                <td>{{$group->id}}</td>
                <td>{{$group->name}}</td>
                <td>{{simpleDateFormat($group->created_at)}}</td>
                <!-- BOTÕES DE AÇÃO -->
                <td>
                  <?php echo simpleAction('EDITAR', 'groups.edit', 'info', 'fa-edit', $group->id); ?>
                  <?php echo simpleAction('USUÀRIOS', 'groups.show', 'info', 'fa-user', $group->id); ?>
                  <?php echo simpleAction('PROGRAMAS', 'programs.show', 'info', 'fa-cogs', $group->id); ?>
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
          <?php echo simpleFootTable($groups) ?>
          <!-- FIM - RODAPE -->

        </div>
      </div>
    </div>
  </div>
</form>
@stop