<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Acrescentei classes
use App\User;
use App\Group;
use App\GroupUser;
use Illuminate\Support\Facades\DB;


class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Obtem quantidade de registros, coluna de ordenação e ordem 
        list($records, $column, $order) = simpleParameters('id');

        // Campos de filtragem
        $name = simpleFilter('name', 'Nome');
        #var_dump(session()->all());
        #var_dump($name);

        // Seleciona os registros, filtra e ordena
        $registros = Group::orderBy($column, $order);
        if ($name) $registros->whereRaw('lower(name) like ?', strtolower("%{$name}%")); # Desconsidera case
        $groups = $registros->paginate($records);

        // Retorna para a view
        return view('admin.groups', compact('groups'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $group = new Group();

        // Retorna para a view
        return view('admin.groups-edit', compact('group'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(Group::$rules);

        // Valida se grupo ja existe (único)
        if (Group::where('name', $request->name)->count())
            throw \Illuminate\Validation\ValidationException::withMessages(['name' => ['Grupo já existente']]);

        $group = new Group();
        $insert = $group->Create($request->all());

        // Verifica se inseriu com sucesso
        if ($insert)
            return redirect()
                ->route('groups.index')
                ->with('success', 'Registro inserido com sucesso!');

        // Verifica se houve erro e passa uma session flash success (sessão temporária)
        return redirect()
            ->back()
            ->with('error', 'Falha ao inserir');
    }


    /**
     * Select group em show users related
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Obtem grupo
        $group = Group::Find($id);
        // Obtem todos registros já associados ao grupo
        $registros = GroupUser::where('group_id', $group->id)->get();
        #dd($registros);
        // Cria array com os grupos ja associados
        $groupUsers = [];
        foreach ($registros as $registro) {
            $groupUsers[$registro->user_id] = $registro;
        }
        // Busca todos usuários e insere na tabela de associação os que estão faltando
        $users = User::all();
        foreach ($users as $user) {
            if (empty($groupUsers[$user->id])) {
                $groupUser = new GroupUser();
                $groupUser->user_id = $user->id;
                $groupUser->group_id = $id;
                $groupUser->active = 'N';
                $groupUser->save();
            }
        }
        // Salva grupo selecionado na memoria
        #session(['selected_id' => $group->id]);
        #session(['selected_name' => $group->name]);
        #dd($group);
        
        // Redireciona para controlador de usuários por grupo
        return redirect()
            ->route('group-users.index')
            ->with('route_back', route('groups.index'))
            ->with('id', $group->id)
            ->with('name', $group->name)
            ->with('success', "Grupo $group->name selecionado");
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        // Obtem usuario
        $group = Group::Find($id);
        // Usuários
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
            ->orderBy('user_name', 'asc')
            ->get();
        #->toSql(); dd($registros);
        $linha = 0;
        $coluna = 0;
        $users = [];
        foreach ($registros as $user) {
            $users[$linha][$coluna++] = $user->user_name;
            if ($coluna > 4) { 
                $linha++; 
                $coluna = 0;
            }
        }
        // Programas
        $registros = DB::table('programs')
            ->leftJoin('group_programs', 'programs.id', '=', 'group_programs.program_id')
            ->leftJoin('groups', 'groups.id', '=', 'group_programs.group_id')
            ->where('group_programs.group_id', $group->id)
            ->select(
                'group_programs.id as id',
                'programs.id as program_id',
                'programs.name as program_name',
                'groups.name as group_name',
                'group_programs.group_id as group_id',
                'group_programs.updated_at',
                'group_programs.active'
            )
            ->orderBy('program_name', 'asc')
            ->get();
        #->toSql(); dd($registros);
        $linha = 0;
        $coluna = 0;
        $programs = [];
        foreach ($registros as $program) {
            $programs[$linha][$coluna++] = $program->program_name;
            if ($coluna > 4) { 
                $linha++; 
                $coluna = 0;
            }
        }
        // Retorna para a view
        return view('admin.groups-edit', compact('group', 'users', 'programs'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate(Group::$rules);

        // Valida se rota ja existe (único)
        if (DB::table('groups')
            ->where('name', $request->name)
            ->where('id', '!=', $id)->count()
        )
            throw \Illuminate\Validation\ValidationException::withMessages(['name' => ['Grupo já existente']]);

        $group = Group::find($id);
        $update = $group->Update($request->all());

        // Verifica se alterou com sucesso
        if ($update)
            return redirect()
                ->route('groups.index')
                ->with('success', 'Registro alterado com sucesso!');

        // Verifica se houve erro e passa uma session flash success (sessão temporária)
        return redirect()
            ->back()
            ->with('error', 'Falha ao alterar');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
