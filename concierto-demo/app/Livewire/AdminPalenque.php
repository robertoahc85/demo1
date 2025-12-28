<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Seat;
use Illuminate\Support\Facades\DB;

class AdminPalenque extends Component
{
    public $seccionFiltro = 'ORO';

    public function actualizarPrecio($seccion, $monto) {
        DB::table('prices')->where('seccion', $seccion)->update(['monto' => $monto]);
    }

    public function cambiarEstado($id) {
        $as = Seat::find($id);
        $as->update(['esta_ocupado' => !$as->esta_ocupado, 'on_hold_until' => null]);
    }

    public function render() {
        return view('livewire.admin-palenque', [
            'asientos' => Seat::where('seccion', $this->seccionFiltro)->get(),
            'precios' => DB::table('prices')->get(),
            'vendidos' => Seat::where('esta_ocupado', true)->count()
        ]);
    }
}