@extends('adminlte::page')

@section('title', 'Usuários')

@section('content')

<!-- MOSTRA MENSAGEM -->
<?php echo simpleMessage() ?>


<!-- LINHA TITULO, PESQUISA/BUSCA E NOVO REGISTRO -->
<form action="/users" method="get">

  <?php echo simpleHeadTable('Cadastro de Usuários', route('users.create')); ?>

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
          <!-- FILTRO POR E-MAIL -->
          <div class="form-group row">
            <label for="email" class="col-sm-2 col-form-label">E-Mail</label>
            <div class="col-sm-10">
              <input class="form-control form-control-sm" id="email" name="email" value="{{session('email')}}" placeholder="E-Mail">
            </div>
          </div>
        </div>

        <!-- FILTRO - SEGUNDA COLUNA -->
        <div class="col">
          <!-- DATA DE -->
          <div class="form-group row">
            <div class="col-sm-2">
              <label for="filtroPeriodo" class="col-form-label">Período</label>
            </div>
            <div class="col-sm-5">
              <div class="input-group">
                <i class="fa fa-calendar-alt fa-2x"></i>
                <input type="date" class="form-control form-control-sm" id="createdAtBegin" name="createdAtBegin" value="{{session('createdAtBegin')}}" placeholder="Início">
              </div>
            </div>
            <!-- ATE -->
            <div class="col-sm-5">
              <div class="input-group">
                <i class="fa fa-calendar-alt fa-2x"></i>
                <input type="date" class="form-control form-control-sm" id="createdAtEnd" name="createdAtEnd" value="{{session('createdAtEnd')}}" placeholder="Final">
              </div>
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
                <th> <?php echo simpleColumn('email', 'E-MAIL') ?></th>
                <th> <?php echo simpleColumn('created_at', 'CRIAÇÂO') ?></th>
                <th> <?php echo simpleColumn('is_admin', 'ADM?') ?></th>
                <th> AÇÔES </th>
              </tr>
            </thead>

            <tbody>
              @foreach($users as $user)
              <tr>
                <td>{{$user->id}}</td>
                <td>{{$user->name}}</td>
                <td>{{$user->email}}</td>
                <td>{{simpleDateFormat($user->created_at)}}</td>
                <td>{{$user->is_admin}}</td>
                <!-- BOTÕES DE AÇÃO -->
                <td>
                  <?php echo simpleAction('EDITAR', 'users.edit', 'info', 'fa-edit', $user->id); ?>
                  <?php
                  echo simpleAction(
                    $user->is_admin == 'S' ? 'Tornar Usuário' : 'Tornar Admin',
                    'users.admin',
                    $user->is_admin == 'S' ? 'danger' : 'success',
                    'fa-edit',
                    $user->id
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
          <?php echo simpleFootTable($users) ?>
          <!-- FIM - RODAPE -->

        </div>
      </div>
    </div>
  </div>
</form>
@stop