<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/licence_validate/{id}', 'Admin\LicenceController@licence_validate')->name('licences.validate');

Auth::routes();

Route::get('/home', function() {
    return view('home');
})->name('home')->middleware('auth');


Route::group(['middleware' => 'auth'], function()
{
    // Cadastro de Usuários
    Route::resources([
        'users' => 'Admin\UserController',
    ]); 
    Route::get('/users/admin/{id}', 'Admin\UserController@admin')->name('users.admin');
    Route::get('/perfil', 'Admin\UserController@perfil')->name('perfil');
    Route::put('/perfil-update', 'Admin\UserController@perfil_update')->name('perfil-update');

    // Cadastro de programas
    Route::resources([
        'programs' => 'Admin\ProgramController',
    ]); 

    // Cadastro de grupos
    Route::resources([
        'groups' => 'Admin\GroupController',
    ]); 

    // Cadastro de usuários no grupo
    Route::resources([
        'group-users' => 'Admin\GroupUserController',
    ]); 

    // Cadastro de programas no grupo
    Route::resources([
        'group-programs' => 'Admin\GroupProgramController',
    ]); 

    // Licenças
    Route::resources([
        'licences' => 'Admin\LicenceController',
    ]); 

});