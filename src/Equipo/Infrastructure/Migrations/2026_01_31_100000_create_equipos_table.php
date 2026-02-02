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
        Schema::create('equipos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('cliente_id');
            $table->string('marca');
            $table->string('modelo');
            $table->string('imei')->unique();
            $table->string('numero_serie')->nullable();
            $table->string('color');
            $table->text('patron_bloqueo')->nullable();
            $table->text('observaciones_iniciales')->nullable();
            $table->enum('estado_fisico', ['Excelente', 'Bueno', 'Regular', 'Malo']);
            $table->json('accesorios')->nullable();
            $table->timestamps();
            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipos');
    }
};
