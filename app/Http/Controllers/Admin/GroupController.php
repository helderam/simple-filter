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
        $registros = GroupUser::where('group_id',$group->id)->get();
        #dd($registros);
        // Cria array com os grupos ja associados
        $groupUsers = [];
        foreach($registros as $registro){
            $groupUsers[$registro->user_id] = $registro;
        }
        // Busca todos usuários e insere na tabela de associação os que estão faltando
        $users = User::all();
        foreach($users as $user){
            if (empty($groupUsers[$user->id])){
                $groupUser = new GroupUser();
                $groupUser->user_id = $user->id;
                $groupUser->group_id = $id;
                $groupUser->active = 'N';
                $groupUser->save();
            }
        }
        // Salva grupo selecionado na memoria
        session(['group' => $group]);
        // Redireciona para controlador de usuários por grupo
        return redirect()
            ->route('group-users.index')
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

        // Retorna para a view
        return view('admin.groups-edit', compact('group'));
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
