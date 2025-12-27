<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Seat;
use Illuminate\Support\Facades\DB;

class PalenqueSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Limpiar datos previos para evitar duplicados
        Seat::truncate();

        // 2. Definición de la estructura del Palenque
        $estructura = [
            'ORO' => [
                'filas' => ['A', 'B', 'C', 'D'], 
                'asientos_por_fila' => 20
            ],
            'PLATA' => [
                'filas' => ['E', 'F', 'G', 'H', 'I'], 
                'asientos_por_fila' => 25
            ],
            'GENERAL' => [
                'filas' => ['J', 'K', 'L', 'M', 'N', 'O'], 
                'asientos_por_fila' => 30
            ],
        ];

        // 3. Poblar la base de datos
        foreach ($estructura as $zona => $config) {
            foreach ($config['filas'] as $fila) {
                for ($numero = 1; $numero <= $config['asientos_por_fila']; $numero++) {
                    Seat::create([
                        'seccion'      => $zona,     // Ejemplo: ORO
                        'fila'         => $fila,     // Ejemplo: A
                        'numero'       => $numero,   // Ejemplo: 1
                        'esta_ocupado' => rand(0, 10) > 9, // 10% de probabilidad de estar vendido
                    ]);
                }
            }
        }

        $this->command->info('¡Palenque de Tlaxcala poblado exitosamente!');
    }
}