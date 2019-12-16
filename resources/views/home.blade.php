@extends('adminlte::page')

@section('title', 'NewSAC5')

@section('content_header')
    <h1 class="m-0 text-dark">Dashboard</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <p class="mb-0">You are logged in!</p>
                    <?php
                    $sessao = session()->all();
                    var_dump($sessao);
                    ?>
                </div>
            </div>
        </div>
    </div>
@stop
