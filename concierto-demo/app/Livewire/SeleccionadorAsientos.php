<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Seat;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SeleccionadorAsientos extends Component
{
    public $paso = 0; 
    public $eventoSeleccionado = null;
    public $seccionActual = 'ORO';
    public $seleccionados = []; 
    public $boletosComprados = [];
    public $tiempoRestante = '05:00';
    public $procesandoPago = false;
    public $conSeguro = true; 
    public $mostrarDetallesMovil = false;

    public $tarjeta_nombre, $tarjeta_numero, $tarjeta_exp, $tarjeta_cvv;

    public $eventos = [
        ['id' => 1, 'artista' => 'Carin León', 'fecha' => '15 Nov', 'imagen' => 'https://loremflickr.com/g/800/600/concert,mexico/all'],
        ['id' => 2, 'artista' => 'Julión Álvarez', 'fecha' => '16 Nov', 'imagen' => 'https://loremflickr.com/g/800/600/singer,stage/all'],
        ['id' => 3, 'artista' => 'Christian Nodal', 'fecha' => '17 Nov', 'imagen' => 'https://loremflickr.com/g/800/600/festival,crowd/all'],
    ];

    public function toggleDetalles() { $this->mostrarDetallesMovil = !$this->mostrarDetallesMovil; }

    public function getPrecio($seccion) { return DB::table('prices')->where('seccion', $seccion)->value('monto') ?? 0; }

    public function seleccionarEvento($id)
    {
        $this->eventoSeleccionado = collect($this->eventos)->firstWhere('id', $id);
        $this->paso = 1;
    }

    public function seleccionarAsiento($id)
    {
        $asiento = Seat::find($id);
        if (!$asiento || $asiento->esta_ocupado || ($asiento->on_hold_until && $asiento->on_hold_until > now())) return;

        if (in_array($id, $this->seleccionados)) {
            $this->seleccionados = array_diff($this->seleccionados, [$id]);
        } else {
            if (count($this->seleccionados) < 6) {
                $asiento->update(['on_hold_until' => now()->addMinutes(5)]);
                $this->seleccionados[] = $id;
            }
        }
    }

    public function irAPagar() { if (!empty($this->seleccionados)) $this->paso = 2; }

    public function finalizarCompra()
    {
        $this->procesandoPago = true;
        sleep(2); 
        $this->boletosComprados = $this->seleccionados;
        Seat::whereIn('id', $this->seleccionados)->update(['esta_ocupado' => true, 'on_hold_until' => null]);
        $this->procesandoPago = false;
        $this->paso = 3;
    }

    public function volverAlInicio() { $this->reset(['seleccionados', 'paso', 'boletosComprados', 'eventoSeleccionado', 'procesandoPago', 'conSeguro', 'mostrarDetallesMovil']); }

    public function render()
    {
        Seat::where('on_hold_until', '<', now())->where('esta_ocupado', false)->update(['on_hold_until' => null]);

        if (!empty($this->seleccionados)) {
            $ultimo = Seat::find(end($this->seleccionados));
            if ($ultimo && $ultimo->on_hold_until) {
                $diff = now()->diff(Carbon::parse($ultimo->on_hold_until));
                $this->tiempoRestante = $diff->invert ? '00:00' : $diff->format('%I:%S');
                if($diff->invert) $this->volverAlInicio(); 
            }
        }

        $subtotal = collect($this->seleccionados)->sum(fn($id) => $this->getPrecio(Seat::find($id)->seccion));
        $costoSeguro = $this->conSeguro ? (count($this->seleccionados) * 45) : 0;

        return view('livewire.seleccionador-asientos', [
            'filas' => Seat::where('seccion', $this->seccionActual)->get()->groupBy('fila'),
            'secciones' => DB::table('prices')->pluck('seccion'),
            'total' => $subtotal + $costoSeguro
        ]);
    }
}