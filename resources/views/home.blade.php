@extends('adminlte::page')

@section('title', 'AGENDA ENTREGA')

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
                    $store_ids = session('store_ids');
                    if (empty($store_ids)) {
                        echo "ATENÇÃO: USUÁRIO SEM LOJA ASSOCIADO !!!";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
@stop
