<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'route', 'description',
    ];

    public static $rules = [
        'name' => "required|max:50",
        'route' => 'required|min:5|max:20', #|unique:users', ATENÇÂO NÂO UTILIZAR UNIQUE POIS NAO FUNCIONA NO UPDATE
    ];
}
