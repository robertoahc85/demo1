<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
 public function up(): void
{
    Schema::create('prices', function (Blueprint $table) {
        $table->id();
        $table->string('seccion')->unique();
        $table->integer('monto');
        $table->timestamps();
    });

    // Insertamos los precios iniciales del Palenque
    DB::table('prices')->insert([
        ['seccion' => 'ORO', 'monto' => 3500],
        ['seccion' => 'PLATA', 'monto' => 1800],
        ['seccion' => 'GENERAL', 'monto' => 600],
    ]);
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
