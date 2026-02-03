<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Primero agregar las nuevas columnas como nullable
        Schema::table('tecnicos', function (Blueprint $table) {
            $table->string('nombre')->nullable()->after('id');
            $table->string('telefono')->nullable()->after('nombre');
            $table->string('email')->nullable()->after('telefono');
        });

        // Migrar datos existentes: copiar nombre del usuario relacionado
        DB::statement("
            UPDATE tecnicos
            SET nombre = COALESCE(
                (SELECT name FROM users WHERE users.id = tecnicos.user_id),
                'Técnico Sin Nombre'
            ),
            email = (SELECT email FROM users WHERE users.id = tecnicos.user_id)
            WHERE user_id IS NOT NULL
        ");

        // Establecer valor por defecto para registros sin user_id
        DB::statement("
            UPDATE tecnicos
            SET nombre = 'Técnico Sin Nombre'
            WHERE nombre IS NULL
        ");

        // Ahora hacer nombre NOT NULL
        Schema::table('tecnicos', function (Blueprint $table) {
            $table->string('nombre')->nullable(false)->change();
        });

        // Finalmente eliminar la foreign key y columna user_id
        Schema::table('tecnicos', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('tecnicos', function (Blueprint $table) {
            // Restaurar user_id
            $table->uuid('user_id')->nullable()->unique()->after('id');
        });

        // Eliminar nuevas columnas
        Schema::table('tecnicos', function (Blueprint $table) {
            $table->dropColumn(['nombre', 'telefono', 'email']);
        });

        // Agregar foreign key
        Schema::table('tecnicos', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
