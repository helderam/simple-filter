@extends('adminlte::page')

@section('title', 'Usuários')

@section('content')

<!-- MOSTRA MENSAGEM E ERROS -->
<?php echo simpleMessage($errors) ?>
<?php echo simpleFormHead('Cadastro Usuário', $user->id) ?> 


<!-- FORMULARIO -->
@if ( isset($user->id) )
<form action="{{ route('users.update', $user->id) }}" method="POST">
  @method('PUT')
  @else
  <form action="{{ route('users.store') }}" method="POST">
    @endif
    @csrf
    <div class="card card-body">
      <div class="row">
        <!--  PRIMEIRA COLUNA -->
        <div class="col">

          <!-- ID -->
          <div class="form-group row">
            <label for="id" class="col-sm-2 col-form-label">Nome</label>
            <div class="col-sm-10">
              <input class="form-control form-control-sm" id="id" name="id" value="{{ old('id', $user->id) }}" placeholder="ID" readonly>
            </div>
          </div>

          <!-- NOME -->
          <div class="form-group row">
            <label for="name" class="col-sm-2 col-form-label">Nome</label>
            <div class="col-sm-10">
              <input class="form-control form-control-sm" id="name" name="name" value="{{ old('name', $user->name) }}" placeholder="Nome">
            </div>
          </div>

          <!-- E-MAIL -->
          <div class="form-group row">
            <label for="email" class="col-sm-2 col-form-label">E-Mail</label>
            <div class="col-sm-10">
              <input class="form-control form-control-sm" id="email" name="email" value="{{ old('email', $user->email) }}" placeholder="E-Mail">
            </div>
          </div>
        </div>

        <!-- SEGUNDA COLUNA -->
        <div class="col">

          <!-- DATA DE -->
          <div class="form-group row">
            <div class="col-sm-2">
              <label for="Criado" class="col-form-label">Craido em</label>
            </div>
            <div class="col-sm-5">
              <div class="input-group">
                <i class="fa fa-calendar-alt fa-2x"></i>
                <input type="date" class="form-control form-control-sm" id="created_at" name="created_at" value="{{ old('created_at', $user->created_at ? $user->created_at->format('Y-m-d') : '') }}">
              </div>
            </div>
          </div>

        </div>

      </div> <!-- row -->

      <!-- BOTÕES -->
      <?php echo simpleFormButtons(route('users.index')) ?>

    </div> <!-- card -->
  </form>
  @stop