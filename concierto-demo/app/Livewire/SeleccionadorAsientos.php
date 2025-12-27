<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Seat;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SeleccionadorAsientos extends Component
{
    public $paso = 1; 
    public $seccionActual = 'ORO';
    public $seleccionados = []; 
    public $boletosComprados = [];
    public $historialCompras = [];
    public $tiempoRestante = '';
    
    // Propiedades Admin
    public $modoAdmin = false;
    public $preciosEditables = [];

    public function mount()
    {
        $this->cargarPrecios();
    }

    public function cargarPrecios()
    {
        $this->preciosEditables = DB::table('prices')->pluck('monto', 'seccion')->toArray();
    }

    public function toggleAdmin()
    {
        $this->modoAdmin = !$this->modoAdmin;
        if($this->modoAdmin) $this->cargarPrecios();
    }

    public function actualizarPrecioBD($seccion, $nuevoMonto)
    {
        DB::table('prices')->where('seccion', $seccion)->update(['monto' => $nuevoMonto]);
        $this->cargarPrecios();
        session()->flash('admin_msg', "Precio de $seccion actualizado.");
    }

    public function forzarEstadoAsiento($id, $estado)
    {
        Seat::find($id)->update(['esta_ocupado' => $estado, 'on_hold_until' => null]);
    }

    public function limpiarExpirados()
    {
        Seat::where('on_hold_until', '<', now())->where('esta_ocupado', false)->update(['on_hold_until' => null]);
    }

    public function getPrecio($seccion)
    {
        return $this->preciosEditables[$seccion] ?? 0;
    }

    public function seleccionarAsiento($id)
    {
        $asiento = Seat::find($id);
        if (!$asiento || $asiento->esta_ocupado || ($asiento->on_hold_until && $asiento->on_hold_until > now())) return;

        if (in_array($id, $this->seleccionados)) {
            $this->seleccionados = array_diff($this->seleccionados, [$id]);
        } else {
            $this->seleccionados[] = $id;
        }
    }

    public function irAPagar()
    {
        if (empty($this->seleccionados)) return;
        Seat::whereIn('id', $this->seleccionados)->update(['on_hold_until' => now()->addMinutes(5)]);
        $this->paso = 2;
    }

    public function finalizarCompra()
    {
        $this->boletosComprados = $this->seleccionados;
        foreach($this->seleccionados as $id) {
            $as = Seat::find($id);
            if($as) $this->historialCompras[] = ['asiento' => $as->numero, 'seccion' => $as->seccion, 'fecha' => now()->format('H:i')];
        }
        Seat::whereIn('id', $this->seleccionados)->update(['esta_ocupado' => true, 'on_hold_until' => null]);
        $this->paso = 3;
    }

    public function volverAlInicio()
    {
        $this->reset(['seleccionados', 'paso', 'boletosComprados', 'tiempoRestante']);
    }

    public function render()
    {
        $this->limpiarExpirados();

        if ($this->paso == 2 && !empty($this->seleccionados)) {
            $primerAsiento = Seat::find($this->seleccionados[0]);
            if ($primerAsiento && $primerAsiento->on_hold_until) {
                $expiresAt = Carbon::parse($primerAsiento->on_hold_until);
                $this->tiempoRestante = now()->greaterThan($expiresAt) ? '00:00' : now()->diff($expiresAt)->format('%I:%S');
            }
        }

        return view('livewire.seleccionador-asientos', [
            'asientos' => Seat::where('seccion', $this->seccionActual)->get(),
            'secciones' => array_keys($this->preciosEditables),
            'total' => collect($this->seleccionados)->sum(fn($id) => $this->getPrecio(Seat::find($id)->seccion))
        ]);
    }
}