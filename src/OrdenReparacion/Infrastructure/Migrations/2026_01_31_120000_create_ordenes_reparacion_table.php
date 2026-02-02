<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes_reparacion', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('numero_orden')->unique();
            $table->uuid('cliente_id');
            $table->uuid('equipo_id');
            $table->uuid('tecnico_id');
            $table->dateTime('fecha_ingreso');
            $table->dateTime('fecha_promesa')->nullable();
            $table->dateTime('fecha_entrega')->nullable();
            $table->text('falla_reportada');
            $table->text('diagnostico_tecnico')->nullable();
            $table->text('solucion_aplicada')->nullable();
            $table->enum('estado', [
                'recibido',
                'en_diagnostico',
                'diagnosticado',
                'pendiente_aprobacion',
                'aprobado',
                'rechazado',
                'en_reparacion',
                'reparado',
                'entregado',
                'cancelado'
            ])->default('recibido');
            $table->enum('prioridad', ['baja', 'media', 'alta', 'urgente'])->default('media');
            $table->decimal('costo_mano_obra', 10, 2)->default(0);
            $table->decimal('costo_repuestos', 10, 2)->default(0);
            $table->decimal('costo_total', 10, 2)->default(0);
            $table->decimal('adelanto', 10, 2)->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('requiere_aprobacion')->default(true);
            $table->boolean('aprobado_por_cliente')->nullable();
            $table->dateTime('fecha_aprobacion')->nullable();
            $table->text('motivo_rechazo')->nullable();
            $table->timestamps();
            
            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('restrict');
            $table->foreign('equipo_id')->references('id')->on('equipos')->onDelete('restrict');
            $table->foreign('tecnico_id')->references('id')->on('users')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordenes_reparacion');
    }
};
