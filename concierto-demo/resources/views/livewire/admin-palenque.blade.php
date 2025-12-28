<div class="min-h-screen bg-[#080808] text-zinc-100 p-6 md:p-12 uppercase tracking-tighter">
    
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
        <div>
            <h1 class="text-4xl font-black italic text-red-700 leading-none">C-PANEL<br><span class="text-white text-lg not-italic font-bold tracking-[0.3em]">ADMINISTRACIÓN PALENQUE</span></h1>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 w-full md:w-auto">
            <div class="bg-zinc-900 p-4 rounded-2xl border border-white/5">
                <p class="text-[8px] font-bold text-zinc-500 mb-1 tracking-widest">Ocupación</p>
                <p class="text-xl font-black italic">{{ number_format(($stats['vendidos'] / $stats['total_asientos']) * 100, 1) }}%</p>
            </div>
            <div class="bg-zinc-900 p-4 rounded-2xl border border-white/5">
                <p class="text-[8px] font-bold text-zinc-500 mb-1 tracking-widest">Ventas Totales</p>
                <p class="text-xl font-black italic text-green-500">${{ number_format($stats['ingresos_proyectados']) }}</p>
            </div>
            <div class="bg-zinc-900 p-4 rounded-2xl border border-white/5 col-span-2 md:col-span-1">
                <p class="text-[8px] font-bold text-zinc-500 mb-1 tracking-widest">Boletos</p>
                <p class="text-xl font-black italic">{{ $stats['vendidos'] }} / {{ $stats['total_asientos'] }}</p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-zinc-900 p-8 rounded-[2.5rem] border border-white/5">
                <h2 class="text-xl font-black italic mb-6 border-b border-white/10 pb-2 uppercase">Ajuste de Precios</h2>
                @if(session()->has('admin_msg'))
                    <div class="bg-green-500/10 text-green-500 p-3 rounded-xl text-[10px] font-bold mb-4 animate-bounce">
                        {{ session('admin_msg') }}
                    </div>
                @endif
                <div class="space-y-4">
                    @foreach($precios as $sec => $monto)
                        <div>
                            <label class="text-[9px] font-bold text-zinc-500 mb-1 block tracking-widest italic">{{ $sec }}</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 font-black text-zinc-500">$</span>
                                <input type="number" 
                                       wire:change="actualizarPrecio('{{ $sec }}', $event.target.value)" 
                                       value="{{ $monto }}"
                                       class="w-full bg-black border border-white/10 rounded-xl p-4 pl-8 font-black text-amber-500 focus:border-red-700 outline-none transition-all">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-zinc-900 p-8 rounded-[2.5rem] border border-white/5">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-xl font-black italic uppercase">Control de Mapa</h2>
                    <div class="flex gap-2">
                        <select wire:model.live="seccionFiltro" class="bg-black border border-white/10 rounded-lg text-[10px] font-black p-2 outline-none uppercase italic">
                            @foreach($secciones as $s) <option value="{{$s}}">{{$s}}</option> @endforeach
                        </select>
                        <button wire:click="liberarTodo" wire:confirm="¿Liberar todos los asientos de esta sección?" class="bg-red-700/10 text-red-700 border border-red-700/20 px-4 py-2 rounded-lg text-[9px] font-black hover:bg-red-700 hover:text-white transition-all">LIMPIAR ZONA</button>
                    </div>
                </div>

                <div class="grid grid-cols-6 md:grid-cols-10 gap-2 overflow-y-auto max-h-[500px] pr-4 custom-scrollbar">
                    @foreach($asientos as $as)
                        <button wire:click="cambiarEstadoAsiento({{ $as->id }})" 
                            class="h-10 rounded-lg border font-black text-[9px] transition-all flex flex-col items-center justify-center relative group
                            {{ $as->esta_ocupado ? 'bg-red-700 border-red-500 text-white' : 'bg-black border-white/10 text-zinc-600 hover:border-green-500' }}">
                            <span class="opacity-40 text-[7px]">{{ $as->fila }}</span>
                            {{ $as->numero }}
                            
                            <span class="absolute -top-8 scale-0 group-hover:scale-100 bg-white text-black px-2 py-1 rounded text-[8px] transition-all z-10 font-black">
                                {{ $as->esta_ocupado ? 'MARCAR LIBRE' : 'MARCAR VENDIDO' }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>