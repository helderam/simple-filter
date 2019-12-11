<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Acrescentei classes
use App\User;
use Illuminate\Support\Facades\DB;
use Adldap\AdldapInterface;

class UserController extends Controller
{
    /**
     * Display a listing of the resource. 
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        #var_dump(session()->all());
        // Obtem quantidade de registros, coluna de ordenação e ordem 
        list($records, $column, $order) = simpleParameters('id');

        // Campos de filtragem
        $name = simpleFilter('name', 'Nome');
        $email = simpleFilter('email', 'E-mail');
        $createdAtBegin = simpleFilter('createdAtBegin', 'Data Inicio');

        // Seleciona os registros, filtra e ordena
        $registros = User::orderBy($column, $order);
        if ($name) $registros->whereRaw('lower(name) like ?', strtolower("%{$name}%")); # Desconsidera case
        if ($email) $registros->where('email', 'like', "%{$email}%");
        if ($createdAtBegin) $registros->where('created_at', '>=', $createdAtBegin);
        $users = $registros->paginate($records);

        #var_dump($users); exit; 
        // Retorna para a view
        return view('admin.users', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = new User();

        // Retorna para a view
        return view('admin.users-edit', compact('user'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(User::$rules);

        // Valida se e-mail ja existe (único)
        if (User::where('email', $request->email)->count())
            throw \Illuminate\Validation\ValidationException::withMessages(['email' => ['E-Mail já existente']]);

        $user = new User();
        $insert = $user->Create($request->all());

        // Verifica se inseriu com sucesso
        if ($insert)
            return redirect()
                ->route('users.index')
                ->with('success', 'Registro inserido com sucesso!');

        // Verifica se houve erro e passa uma session flash success (sessão temporária)
        return redirect()
            ->back()
            ->with('error', 'Falha ao inserir');
    }

    /**
     * Change usar statu ADMIN ou USER NORMAL
     *
     * @return \Illuminate\Http\Response
     */
    public function admin($id)
    {
        $user = User::FindOrFail($id);
        if ($user) {
            $user->is_admin = $user->is_admin == 'S' ? 'N' : 'S';
            $user->save();
            return redirect()->route('users.index')->with('message', 'Usuario ajustado!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $user = User::Find($id);

        // Retorna para a view
        return view('admin.users-edit', compact('user'));
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
        $request->validate(User::$rules);

        // Valida se e-mail ja existe (único)
        if (DB::table('users')
            ->where('email', $request->email)
            ->where('id', '!=', $id)->count()
        )
            throw \Illuminate\Validation\ValidationException::withMessages(['email' => ['E-Mail já existente']]);

        $user = User::find($id);
        $update = $user->Update($request->all());

        // Verifica se alterou com sucesso
        if ($update)
            return redirect()
                ->route('users.index')
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
