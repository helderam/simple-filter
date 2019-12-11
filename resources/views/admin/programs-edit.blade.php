@extends('adminlte::page')

@section('title', 'Usuários')

@section('content')

<!-- MOSTRA MENSAGEM E ERROS -->
{!! simpleMessage($errors) !!}
{!! simpleFormHead('Cadastro Usuário', $program->id) !!}


<!-- FORMULARIO -->
@if ( isset($program->id) )
{!! Form::model($program, ['route' => ['programs.update', $program->id], 'class' => 'form']) !!}
{!! Form::hidden('_method', 'PUT') !!}
@else
{!! Form::open(['route' => 'programs.store']) !!}
@endif
<div class="card card-body">
  <div class="row">
    <!--  PRIMEIRA COLUNA -->
    <div class="col">

      <!-- ID -->
      <div class="form-group row">
        {!! Form::label('id', 'ID:', ['class' => 'col-sm-2 col-form-label']) !!}
        <div class="col-sm-10">
          {!! Form::text('id', old('id', $program->id), ['placeholder' => 'ID', 'class' => 'form-control form-control-sm', 'readonly']) !!}
        </div>
      </div>

      <!-- NOME -->
      <div class="form-group row">
        {!! Form::label('name', 'Nome Programa:', ['class' => 'col-sm-2 col-form-label']) !!}
        <div class="col-sm-10">
          {!! Form::text('name', old('name', $program->name), ['placeholder' => 'Nome do Programa', 'class' => 'form-control form-control-sm']) !!}
        </div>
      </div>

      <!-- ROTA -->
      <div class="form-group row">
        {!! Form::label('route', 'Rota:', ['class' => 'col-sm-2 col-form-label']) !!}
        <div class="col-sm-10">
          {!! Form::text('route', old('route', $program->route), ['placeholder' => 'Rota', 'class' => 'form-control form-control-sm']) !!}
        </div>
      </div>
    </div>

    <!-- SEGUNDA COLUNA -->
    <div class="col">

      <!-- DATA DE CRIAÇÂO -->
      <div class="form-group row">
        <div class="col-sm-2">
          <label for="created_at" class="col-form-label">Criado em</label>
        </div>
        <div class="col-sm-5">
          <div class="input-group">
            <i class="fa fa-calendar-alt fa-2x"></i>
            <input type="datetime-local" class="form-control form-control-sm" id="created_at" name="created_at" readonly value="{{ old('created_at', $program->created_at ? $program->created_at->format('Y-m-d\TH:i:s') : '') }}">
          </div>
        </div>
      </div>

      <!-- DATA DE ALTERAÇÂO -->
      <div class="form-group row">
        <div class="col-sm-2">
          <label for="updated_at" class="col-form-label">Alterado em</label>
        </div>
        <div class="col-sm-5">
          <div class="input-group">
            <i class="fa fa-calendar-alt fa-2x"></i>
            <input type="datetime-local" class="form-control form-control-sm" id="updated_at" name="updated_at" readonly value="{{ old('updated_at', $program->updated_at ? $program->updated_at->format('Y-m-d\TH:i:s') : '') }}">
          </div>
        </div>
      </div>

      <!-- DESCRIÇÂO -->
      <div class="form-group row">
        <div class="col-sm-2">
          <label for="description" class="col-form-label">Descrição</label>
        </div>
        <div class="col-sm-5">
          <div class="input-group">
            <textarea class="form-control form-control-sm" id="description" name="description" rows="3">{{ old('description', $program->description) }}</textarea>
          </div>
        </div>
      </div>

    </div>

  </div> <!-- row -->

  <!-- BOTÕES -->
  <?php echo simpleFormButtons(route('programs.index')) ?>

</div> <!-- card -->
{!! Form::close() !!}
@stop