<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Acrescentei classes
use App\User;
use App\GroupUser;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\GroupUse;

class GroupUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $group = session('group');
        if (empty($group))
            return redirect()->route('home')->with('error', 'Sessão Expirada');
        #Redirect::to('home')->send();

        #var_dump(session()->all());
        // Obtem quantidade de registros, coluna de ordenação e ordem 
        list($records, $column, $order) = simpleParameters('active,id', 'asc');

        // Campos de filtragem
        $name = simpleFilter('name', 'Nome');
        $email = simpleFilter('email', 'EMail');

        #$sql = $registros->toSql(); dd($sql); 

        // Seleciona os registros, filtra e ordena
        $registros = DB::table('users')
            ->leftJoin('group_users', 'users.id', '=', 'group_users.user_id')
            ->leftJoin('groups', 'groups.id', '=', 'group_users.group_id')
            ->where('group_users.group_id', $group->id)
            ->select(
                'group_users.id as id',
                'users.id as user_id',
                'users.name as user_name',
                'users.email',
                'groups.name as group_name',
                'group_users.group_id as group_id',
                'group_users.updated_at',
                'group_users.active'
            )
            ->orderBy($column, $order);

        if ($name) $registros->whereRaw('lower(bi_users.name) like ?', strtolower("%{$name}%")); # Desconsidera case
        if ($email) $registros->whereRaw('lower(bi_users.email) like ?', strtolower("%{$email}%")); # Desconsidera case
        $groupUsers = $registros->paginate($records);

        // Retorna para a view
        return view('admin.group-users', compact('groupUsers'));
    }

    /**
     * Include/Remove user into group
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $groupUser = GroupUser::Find($id);
        if ($groupUser->active == 'S') {
            // Exclui caso ja exista
            $groupUser->active = 'N';
            $mensagem = "Usuário excluido";
        } else {
            // Inclui caso não exista
            $groupUser->active = "S";
            $mensagem = "Usuário incluido";
        }
        $groupUser->save();
        return redirect()->route('group-users.index')->with('message', $mensagem);
    }
}
