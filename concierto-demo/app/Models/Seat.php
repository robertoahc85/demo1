<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    //
protected $fillable = ['seccion', 'fila', 'numero', 'esta_ocupado', 'on_hold_until'];
}
