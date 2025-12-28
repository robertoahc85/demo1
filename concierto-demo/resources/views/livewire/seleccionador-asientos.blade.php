<div wire:poll.1s class="min-h-screen bg-[#050505] text-amber-50 font-sans antialiased pb-32 lg:pb-20 uppercase tracking-tighter">
    
    <nav class="bg-zinc-900/95 border-b border-amber-900/30 p-4 sticky top-0 z-50 backdrop-blur-md print:hidden">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <button wire:click="volverAlInicio" class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-700 rounded-full flex items-center justify-center font-black border border-amber-500/30 shadow-lg italic text-white text-xl">P</div>
                <div class="text-left leading-none">
                    <span class="font-black text-xl md:text-2xl italic text-white uppercase block">CONCIERTO<span class="text-red-600">APP</span></span>
                    <span class="text-[7px] md:text-[8px] font-bold text-amber-600 tracking-[0.3em] block mt-1">PALENQUE TLAXCALA</span>
                </div>
            </button>
            @if(!empty($seleccionados) && $paso < 3)
                <div class="bg-red-700/20 border border-red-700 px-3 py-1.5 rounded-xl text-red-500 font-mono text-xs font-black animate-pulse">
                    {{ $tiempoRestante }}
                </div>
            @endif
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 mt-8">
        @if($paso == 0)
            <div class="text-center mb-12 animate-in fade-in duration-700">
                <h2 class="text-5xl font-black italic text-amber-500 tracking-tighter uppercase">Cartelera Palenque</h2>
                <p class="text-zinc-500 text-xs font-bold tracking-[0.4em] mt-2 italic">Feria de Tlaxcala 2025</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-in fade-in duration-1000">
                @foreach($eventos as $evento)
                    <div class="bg-zinc-900 border border-amber-900/20 rounded-[2.5rem] overflow-hidden group hover:border-red-600 transition-all cursor-pointer relative h-[380px]" wire:click="seleccionarEvento({{ $evento['id'] }})">
                        <img src="{{ $evento['imagen'] }}" class="h-full w-full object-cover opacity-40 group-hover:opacity-100 transition-all duration-700 group-hover:scale-105">
                        <div class="absolute bottom-0 p-8 w-full bg-gradient-to-t from-black text-center">
                            <h3 class="text-3xl font-black italic text-white uppercase">{{ $evento['artista'] }}</h3>
                            <p class="text-amber-500 font-black text-xs mt-1">{{ $evento['fecha'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

        @elseif($paso == 1)
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 animate-in slide-in-from-right-4 duration-500">
                <div class="lg:col-span-8 bg-[#120f06] border border-amber-900/20 rounded-[2.5rem] p-6 md:p-10">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 border-b border-white/10 pb-6 gap-4">
                        <h2 class="text-2xl font-black italic uppercase text-white leading-none">Zona {{ $seccionActual }}</h2>
                        <div class="flex bg-black p-1 rounded-xl border border-white/10 overflow-x-auto max-w-full no-scrollbar">
                            @foreach($secciones as $sec)
                                <button wire:click="$set('seccionActual', '{{$sec}}')" class="px-4 py-2 rounded-lg text-[9px] font-black uppercase transition-all whitespace-nowrap {{ $seccionActual == $sec ? 'bg-red-700 text-white shadow-lg' : 'text-zinc-500' }}">{{ $sec }}</button>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="space-y-4 mb-20 md:mb-0">
                        @foreach($filas as $filaNom => $asientosEnFila)
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-9 bg-zinc-900 rounded-lg flex-shrink-0 flex items-center justify-center text-[9px] font-black text-amber-700 border border-white/5 italic">F{{ $filaNom }}</div>
                                <div class="flex-1 grid grid-cols-5 sm:grid-cols-10 gap-2">
                                    @foreach($asientosEnFila as $as)
                                        <button wire:click="seleccionarAsiento({{ $as->id }})" @if($as->esta_ocupado) disabled @endif
                                            class="h-9 rounded-lg border-2 transition-all font-black text-[10px]
                                            {{ $as->esta_ocupado ? 'bg-black border-zinc-900 opacity-20 cursor-not-allowed' : (in_array($as->id, $seleccionados) ? 'bg-red-700 text-white border-red-400 scale-110 shadow-lg' : ($as->numero % 2 == 0 ? 'bg-zinc-800' : 'bg-zinc-900') . ' border-white/5 text-amber-900 hover:border-amber-500') }}">
                                            {{ $as->numero }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="hidden lg:block lg:col-span-4">
                    <div class="bg-white text-black p-7 rounded-[2.5rem] shadow-2xl sticky top-24 border-t-[10px] border-red-700">
                        <h3 class="font-black italic text-xl mb-5 uppercase border-b border-zinc-100 pb-2">Resumen</h3>
                        <div class="space-y-2 mb-6 max-h-48 overflow-y-auto pr-1">
                            @foreach($seleccionados as $id)
                                @php $as = \App\Models\Seat::find($id); @endphp
                                <div class="flex justify-between items-center p-3 bg-zinc-50 rounded-xl border border-zinc-100 italic animate-in slide-in-from-right-2">
                                    <span class="text-[9px] font-black uppercase text-red-700">F{{ $as->fila }} - #{{ $as->numero }}</span>
                                    <button wire:click="seleccionarAsiento({{ $id }})" class="text-zinc-300 hover:text-red-600 transition-colors">✕</button>
                                </div>
                            @endforeach
                        </div>

                        @if(!empty($seleccionados))
                        <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-2xl flex items-start gap-3">
                            <input type="checkbox" wire:model.live="conSeguro" class="mt-1 accent-red-700 h-4 w-4">
                            <div>
                                <p class="text-[9px] font-black uppercase italic leading-none text-zinc-900">Seguro de Protección</p>
                                <p class="text-[7px] font-bold text-zinc-500 mt-1 leading-tight normal-case">Protección contra imprevistos. <span class="text-red-700 font-black">+$45 c/u</span></p>
                            </div>
                        </div>
                        @endif

                        <div class="flex justify-between items-center mb-6 pt-4 border-t border-zinc-100">
                            <span class="text-2xl font-black italic">Total:</span>
                            <span class="text-2xl font-black text-red-700 italic">${{ number_format($total) }}</span>
                        </div>
                        <button wire:click="irAPagar" class="w-full bg-red-700 text-white py-4 rounded-2xl font-black uppercase shadow-xl hover:bg-red-800 transition-all text-sm italic">Ir al Pago</button>
                    </div>
                </div>
            </div>

            @if(!empty($seleccionados))
            <div class="lg:hidden fixed bottom-0 left-0 w-full z-[100] animate-in slide-in-from-bottom-full duration-500">
                @if($mostrarDetallesMovil)
                <div class="bg-white text-black p-6 rounded-t-[2.5rem] border-t-4 border-amber-500 shadow-2xl animate-in slide-in-from-bottom-10">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="font-black italic text-sm uppercase">Detalle de Asientos</h4>
                        <button wire:click="toggleDetalles" class="text-zinc-400 font-bold uppercase text-[10px]">Cerrar</button>
                    </div>
                    <div class="space-y-2 mb-4 max-h-40 overflow-y-auto">
                        @foreach($seleccionados as $id)
                            @php $as = \App\Models\Seat::find($id); @endphp
                            <div class="flex justify-between items-center p-3 bg-zinc-50 rounded-xl border border-zinc-100 animate-in slide-in-from-right-2">
                                <span class="text-[9px] font-black uppercase italic text-red-700">F{{ $as->fila }} - #{{ $as->numero }} ({{ $as->seccion }})</span>
                                <span class="text-[10px] font-black text-zinc-400 italic">${{ number_format($this->getPrecio($as->seccion)) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
                <div class="bg-white text-black p-5 border-t-4 border-red-700 shadow-2xl flex justify-between items-center">
                    <div wire:click="toggleDetalles" class="cursor-pointer">
                        <p class="text-[8px] font-bold text-zinc-400 uppercase leading-none">Total ({{ count($seleccionados) }}) • <span class="text-red-700 underline italic">Detalles</span></p>
                        <p class="text-xl font-black text-red-700 italic mt-1 leading-none">${{ number_format($total) }}</p>
                    </div>
                    <button wire:click="irAPagar" class="bg-red-700 text-white px-8 py-3.5 rounded-2xl font-black uppercase text-xs italic tracking-widest shadow-lg shadow-red-200 active:scale-95 transition-all">
                        Continuar
                    </button>
                </div>
            </div>
            @endif

        @elseif($paso == 2)
            <div class="max-w-xl mx-auto py-6 animate-in zoom-in duration-300">
                <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border-t-8 border-red-700 text-zinc-900">
                    <div class="bg-red-700 p-8 text-white flex justify-between items-center font-black italic uppercase">
                        <h2 class="text-2xl italic">Checkout</h2>
                        <span class="text-xl font-mono">{{ $tiempoRestante }}</span>
                    </div>
                    <div class="p-8 space-y-4">
                        <input type="text" wire:model="tarjeta_nombre" placeholder="Nombre en tarjeta" class="w-full p-4 bg-zinc-50 border-2 border-zinc-100 rounded-xl outline-none focus:border-red-700 font-bold uppercase italic text-sm">
                        <input type="text" wire:model="tarjeta_numero" maxlength="16" placeholder="Número de Tarjeta" class="w-full p-4 bg-zinc-50 border-2 border-zinc-100 rounded-xl outline-none focus:border-red-700 font-mono text-base font-bold">
                        <button wire:click="finalizarCompra" wire:loading.attr="disabled" class="w-full bg-red-700 text-white py-5 rounded-2xl font-black text-lg uppercase italic shadow-2xl flex items-center justify-center gap-4 hover:bg-red-800 transition-all">
                            <svg wire:loading wire:target="finalizarCompra" class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle class="opacity-25" cx="12" cy="12" r="10" stroke-width="4"></circle><path class="opacity-75" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            <span wire:loading.remove wire:target="finalizarCompra">Pagar Ahora</span>
                            <span wire:loading wire:target="finalizarCompra">Procesando...</span>
                        </button>
                    </div>
                </div>
            </div>

        @elseif($paso == 3)
            <div class="max-w-md mx-auto py-6 animate-in zoom-in duration-500">
                <div id="ticket-printable" class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden text-zinc-950 border-b-[15px] border-red-700 text-center p-10">
                    <h2 class="text-3xl font-black italic uppercase italic text-black mb-1 leading-none">{{ $eventoSeleccionado['artista'] }}</h2>
                    <p class="text-[9px] font-bold text-amber-600 tracking-widest uppercase mb-8 italic">Tlaxcala 2025</p>
                    
                    <div class="bg-zinc-50 p-6 rounded-[2rem] border-2 border-dashed border-zinc-200 mb-8 inline-block shadow-inner relative">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=TLAX-{{ implode('-', $boletosComprados) }}" class="w-32 h-32 md:w-40 md:h-40 mix-blend-multiply opacity-90 grayscale">
                    </div>
                    
                    <div class="space-y-4 mb-8 print:hidden">
                        <button onclick="window.print()" class="w-full h-12 bg-red-700 text-white rounded-2xl flex items-center justify-center gap-3 shadow-xl font-black text-[10px] italic uppercase tracking-widest">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                            Imprimir Ticket
                        </button>

                        <div class="grid grid-cols-2 gap-3">
                            <button class="h-12 bg-black rounded-xl flex items-center justify-center gap-2 hover:opacity-90 shadow-xl transition-all">
                                <svg width="18" height="18" viewBox="0 0 512 512" fill="white"><path d="M422.5 352.1c-14.9-10.7-33-17.1-52.6-17.1-41.9 0-76 34.1-76 76 0 19.6 7.4 37.4 19.5 50.8L44.8 461.8c-17 0-30.8-13.8-30.8-30.8V81c0-17 13.8-30.8 30.8-30.8h422.4c17 0 30.8 13.8 30.8 30.8v222.9l-75.5 48.2z"/></svg>
                                <span class="text-white font-black text-[8px] italic uppercase">Add to Apple Wallet</span>
                            </button>
                            <button class="h-12 bg-white border border-zinc-200 rounded-xl flex items-center justify-center gap-2 hover:bg-zinc-50 shadow-md transition-all">
                                <svg width="20" height="20" viewBox="0 0 48 48"><path fill="#4285F4" d="M45.12 24.5c0-1.56-.14-3.06-.4-4.5H24v8.51h11.84c-.51 2.75-2.06 5.08-4.39 6.64v5.52h7.11c4.16-3.83 6.56-9.47 6.56-16.17z"/><path fill="#34A853" d="M24 46c5.94 0 10.92-1.97 14.56-5.33l-7.11-5.52c-1.97 1.32-4.49 2.1-7.45 2.1-5.73 0-10.58-3.87-12.31-9.07H4.34v5.7C7.96 41.07 15.4 46 24 46z"/><path fill="#FBBC05" d="M11.69 28.18c-.44-1.32-.69-2.73-.69-4.18s.25-2.86.69-4.18v-5.7H4.34C2.85 17.09 2 20.45 2 24s.85 6.91 2.34 9.89l7.35-5.71z"/><path fill="#EA4335" d="M24 10.75c3.23 0 6.13 1.11 8.41 3.29l6.31-6.31C34.91 4.18 29.93 2 24 2 15.4 2 7.96 6.93 4.34 14.12l7.35 5.7c1.73-5.2 6.58-9.07 12.31-9.07z"/></svg>
                                <span class="text-zinc-900 font-black text-[8px] italic uppercase">Google Wallet</span>
                            </button>
                        </div>
                        
                        <button wire:click="volverAlInicio" class="text-red-700 font-black text-[10px] uppercase tracking-[0.4em] hover:underline">Regresar al inicio</button>
                    </div>
                </div>
            </div>

            <style>
                @media print {
                    body * { visibility: hidden; }
                    #ticket-printable, #ticket-printable * { visibility: visible; }
                    #ticket-printable { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none; border: none; }
                }
            </style>
        @endif
    </div>
</div>