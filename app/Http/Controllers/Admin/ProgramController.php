<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Acrescentei classes
use App\Program;
use App\Group;
use App\GroupProgram;
use Illuminate\Support\Facades\DB;


class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        #var_dump(session()->all());
        // Obtem quantidade de registros, coluna de ordenação e ordem 
        list($records, $column, $order) = simpleParameters('id');

        // Campos de filtragem
        $name = simpleFilter('name', 'Nome');
        $route = simpleFilter('route', 'route');

        // Seleciona os registros, filtra e ordena
        $registros = Program::orderBy($column, $order);
        if ($name) $registros->whereRaw('lower(name) like ?', strtolower("%{$name}%")); # Desconsidera case
        if ($route) $registros->where('route', 'like', "%{$route}%");
        $programs = $registros->paginate($records);

        // Retorna para a view
        return view('admin.programs', compact('programs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $program = new Program();

        // Retorna para a view
        return view('admin.programs-edit', compact('program'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(Program::$rules);

        // Valida se e-mail ja existe (único)
        if (Program::where('route', $request->route)->count())
            throw \Illuminate\Validation\ValidationException::withMessages(['route' => ['Rota já existente']]);

        $program = new Program();
        $insert = $program->Create($request->all());

        // Verifica se inseriu com sucesso
        if ($insert)
            return redirect()
                ->route('programs.index')
                ->with('success', 'Registro inserido com sucesso!');

        // Verifica se houve erro e passa uma session flash success (sessão temporária)
        return redirect()
            ->back()
            ->with('error', 'Falha ao inserir');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Obtem grupo
        $group = Group::Find($id);
        // Obtem todos registros já associados ao grupo
        $registros = GroupProgram::where('group_id',$group->id)->get();
        #dd($registros);
        // Cria array com os programas ja associados
        $groupPrograms = [];
        foreach($registros as $registro){
            $groupPrograms[$registro->program_id] = $registro;
        }
        // Busca todos programas e insere na tabela de associação os que estão faltando
        $programs = Program::all();
        foreach($programs as $program){
            if (empty($groupPrograms[$program->id])){
                $groupProgram = new GroupProgram();
                $groupProgram->program_id = $program->id;
                $groupProgram->group_id = $id;
                $groupProgram->save();
            }
        }
        // Salva grupo selecionado na memoria
        session(['group' => $group]);
        // Redireciona para controlador de programas por grupo
        return redirect()
            ->route('group-programs.index')
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
        $program = Program::Find($id);

        // Retorna para a view
        return view('admin.programs-edit', compact('program'));
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
        $request->validate(Program::$rules);

        // Valida se rota ja existe (único)
        if (DB::table('programs')
            ->where('route', $request->route)
            ->where('id', '!=', $id)->count()
        )
            throw \Illuminate\Validation\ValidationException::withMessages(['route' => ['Rota já existente']]);

        $program = Program::find($id);
        $update = $program->Update($request->all());

        // Verifica se alterou com sucesso
        if ($update)
            return redirect()
                ->route('programs.index')
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
