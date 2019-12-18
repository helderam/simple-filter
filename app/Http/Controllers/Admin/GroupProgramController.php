<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Acrescentei classes
use App\Program;
use App\GroupProgram;
use Illuminate\Support\Facades\DB;


class GroupProgramController extends Controller
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
        list($records, $column, $order) = simpleParameters('active', 'desc');

        // Recupera o grupo selecionado na sessão
        $selected_id = session('selected_id');
        if (empty($selected_id)) return redirect()->route('home');

        // Campos de filtragem
        $name = simpleFilter('name', 'Nome');

        // Seleciona os registros, filtra e ordena
        $registros = DB::table('programs')
            ->leftJoin('group_programs', 'programs.id', '=', 'group_programs.program_id')
            ->leftJoin('groups', 'groups.id', '=', 'group_programs.group_id')
            ->where('group_programs.group_id', $selected_id)
            ->select(
                'group_programs.id as id',
                'programs.id as program_id',
                'programs.name as program_name',
                'programs.route',
                'groups.name as group_name',
                'group_programs.group_id as group_id',
                'group_programs.updated_at',
                'group_programs.active'
            )
            ->orderBy($column, $order);
            #->toSql();
        #dd($registros); 
        if ($name) $registros->whereRaw('lower(programs.name) like ?', strtolower("%{$name}%")); # Desconsidera case
        $groupPrograms = $registros->paginate($records);

        // Retorna para a view
        return view('admin.group-programs', compact('groupPrograms'));
    }

    /**
     * Include/Remove program into group
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $groupProgram = GroupProgram::Find($id);
        #dd($groupProgram);
        if ($groupProgram->active == 'S') {
            // Exclui caso ja exista
            $groupProgram->active = 'N';
            $mensagem = "Programa excluido";
        } else {
            // Inclui caso não exista
            $groupProgram->active = "S";
            $mensagem = "Programa incluido";
        }
        $groupProgram->save();
        return redirect()->route('group-programs.index')->with('message', $mensagem);
    }

}
