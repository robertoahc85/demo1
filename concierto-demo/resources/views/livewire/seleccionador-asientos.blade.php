<div wire:poll.1s class="min-h-screen bg-[#080705] text-amber-50 font-sans antialiased pb-20 relative">
    
    <button wire:click="toggleAdmin" class="fixed bottom-6 right-6 z-[100] bg-red-700 p-4 rounded-full shadow-2xl hover:scale-110 transition-all border border-amber-500/50">
        {!! $modoAdmin ? '❌' : '⚙️' !!}
    </button>

    @if($modoAdmin)
        <div class="fixed inset-0 bg-black/95 z-[90] p-6 md:p-12 overflow-y-auto animate-in fade-in">
            <div class="max-w-5xl mx-auto">
                <h2 class="text-4xl font-black italic uppercase text-amber-500 mb-8 tracking-tighter">Administración Palenque</h2>
                <div class="grid md:grid-cols-2 gap-8 mb-12">
                    <div class="bg-zinc-900 p-6 rounded-[2rem] border border-amber-900/30">
                        <h3 class="text-xs font-bold uppercase text-zinc-500 mb-4 tracking-widest">Precios en Base de Datos</h3>
                        @foreach($preciosEditables as $sec => $monto)
                            <div class="flex items-center gap-4 mb-3 bg-black/40 p-3 rounded-xl">
                                <span class="w-20 font-black italic">{{ $sec }}</span>
                                <input type="number" wire:change="actualizarPrecioBD('{{$sec}}', $event.target.value)" value="{{$monto}}" class="bg-zinc-800 border-none rounded-lg p-2 text-amber-400 w-32 font-bold">
                            </div>
                        @endforeach
                    </div>
                    <div class="bg-zinc-900 p-6 rounded-[2rem] border border-amber-900/30">
                        <h3 class="text-xs font-bold uppercase text-zinc-500 mb-4 tracking-widest">Estado Seccion Actual</h3>
                        <p class="text-[10px] text-zinc-400 mb-4 uppercase">Haz clic para ocupar/liberar</p>
                        <div class="grid grid-cols-6 gap-2">
                            @foreach($asientos as $as)
                                <button wire:click="forzarEstadoAsiento({{$as->id}}, {{$as->esta_ocupado ? 0 : 1}})" class="p-2 rounded font-black text-[10px] {{ $as->esta_ocupado ? 'bg-red-700' : 'bg-green-800/30 border border-green-800' }}">
                                    {{ $as->numero }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <nav class="bg-[#1a1608] border-b border-amber-900/30 p-4 sticky top-0 z-50 backdrop-blur-md">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-700 rounded-full flex items-center justify-center font-black text-white shadow-lg border border-amber-500/30 uppercase italic">P</div>
                <div><span class="font-black text-xl tracking-tighter block leading-none uppercase italic">CONCIERTO<span class="text-red-600">APP</span></span></div>
            </div>
            <div class="flex gap-2">
                @foreach($secciones as $sec)
                    <button wire:click="$set('seccionActual', '{{$sec}}')" class="px-4 py-2 rounded-xl text-[10px] font-black uppercase transition-all {{ $seccionActual == $sec ? 'bg-red-700 text-white' : 'bg-black text-zinc-500' }}">{{ $sec }}</button>
                @endforeach
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 mt-8">
        @if($paso == 1)
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 animate-in fade-in">
                <div class="lg:col-span-8 bg-[#120f06] border border-amber-900/20 rounded-[3rem] p-10 text-center">
                    <div class="w-48 h-20 bg-amber-900/10 border-2 border-amber-600/20 rounded-[100%] mx-auto flex items-center justify-center mb-12">
                        <span class="text-[9px] font-black tracking-[0.5em] text-amber-700 uppercase italic">Ruedo</span>
                    </div>
                    <div class="grid grid-cols-5 sm:grid-cols-8 gap-3">
                        @foreach($asientos as $asiento)
                            <button wire:click="seleccionarAsiento({{ $asiento->id }})" @if($asiento->esta_ocupado) disabled @endif class="h-14 rounded-2xl border-2 transition-all flex flex-col items-center justify-center
                                {{ $asiento->esta_ocupado ? 'bg-zinc-950 border-zinc-900 opacity-20' : (in_array($asiento->id, $seleccionados) ? 'bg-red-700 border-red-500 scale-110 shadow-xl shadow-red-700/40' : 'bg-zinc-900 border-amber-900/10 hover:border-amber-500 text-amber-700') }}">
                                <span class="text-[8px] opacity-50 font-bold">F {{ $asiento->fila }}</span>
                                <span class="text-xs font-black">{{ $asiento->numero }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="lg:col-span-4">
                    <div class="bg-white text-zinc-950 rounded-[2.5rem] p-8 shadow-2xl border-t-8 border-red-700 sticky top-24">
                        <h3 class="font-black uppercase italic text-xl mb-6">Tu Orden</h3>
                        <div class="space-y-3 mb-8 max-h-48 overflow-y-auto">
                            @forelse($seleccionados as $id)
                                @php $as = \App\Models\Seat::find($id); @endphp
                                <div class="flex justify-between items-center p-3 bg-zinc-50 rounded-xl border border-amber-100">
                                    <span class="text-[10px] font-black uppercase tracking-tighter italic">Fila {{ $as->fila }} - #{{ $as->numero }}</span>
                                    <span class="font-black text-red-700 italic">${{ number_format($this->getPrecio($as->seccion)) }}</span>
                                </div>
                            @empty
                                <p class="text-center text-zinc-400 py-10 font-bold uppercase text-[9px] tracking-widest">Selecciona tus lugares</p>
                            @endforelse
                        </div>
                        <div class="flex justify-between items-center mb-6">
                            <span class="text-2xl font-black italic uppercase">Total:</span>
                            <span class="text-2xl font-black text-red-700 italic">${{ number_format($total) }}</span>
                        </div>
                        <button wire:click="irAPagar" @if(empty($seleccionados)) disabled @endif class="w-full bg-red-700 text-white py-5 rounded-2xl font-black uppercase tracking-[0.2em] shadow-xl hover:bg-red-800 disabled:opacity-10">Confirmar</button>
                    </div>
                </div>
            </div>
        @elseif($paso == 2)
            <div class="max-w-xl mx-auto py-10 animate-in zoom-in">
                <div class="bg-white rounded-[3rem] shadow-2xl overflow-hidden border-t-8 border-red-700 text-zinc-900 text-center p-12">
                    <div class="bg-red-50 w-24 h-24 rounded-full flex flex-col items-center justify-center mx-auto mb-6 border border-red-100">
                        <span class="text-[9px] font-bold text-red-700 uppercase">Expira</span>
                        <span class="text-3xl font-black font-mono text-red-700">{{ $tiempoRestante }}</span>
                    </div>
                    <h2 class="text-3xl font-black italic uppercase tracking-tighter">Finalizar Pago</h2>
                    <button wire:click="finalizarCompra" class="w-full bg-red-700 text-white py-6 rounded-3xl font-black text-xl uppercase italic shadow-2xl mt-8">Pagar ${{ number_format($total) }}</button>
                    <button wire:click="$set('paso', 1)" class="mt-6 text-[10px] font-black uppercase tracking-widest text-zinc-400">Volver</button>
                </div>
            </div>
        @elseif($paso == 3)
            <div class="max-w-md mx-auto py-10 animate-in slide-in-from-bottom-10">
                <div class="bg-white rounded-[3rem] shadow-2xl overflow-hidden text-zinc-950 border-b-[15px] border-red-700">
                    <div class="bg-[#1a1608] p-8 text-amber-500 text-center border-b-4 border-amber-600/20">
                        <h2 class="text-2xl font-black italic uppercase leading-none italic">Boleto Palenque</h2>
                        <p class="text-[9px] uppercase font-bold text-amber-600 mt-2 tracking-widest italic">Tlaxcala 2025</p>
                    </div>
                    <div class="p-10 text-center">
                        <div class="bg-zinc-50 p-6 rounded-[2rem] border-2 border-dashed border-zinc-200 mb-8 inline-block shadow-inner relative">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=CONCIERTOAPP-{{ implode('-', $boletosComprados) }}" class="w-40 h-40 mix-blend-multiply opacity-80">
                        </div>
                        <div class="flex justify-between text-left mb-6 font-black italic uppercase text-xs">
                            <div><p class="text-[8px] text-zinc-400 not-italic uppercase">Zona</p>{{ $seccionActual }}</div>
                            <div class="text-right"><p class="text-[8px] text-zinc-400 not-italic uppercase">Tickets</p>{{ count($boletosComprados) }}</div>
                        </div>
                        <button wire:click="volverAlInicio" class="w-full bg-zinc-900 text-white py-4 rounded-xl font-black uppercase text-xs tracking-widest">Finalizar</button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>