<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
           // Obtenemos la instancia nativa de MongoDB
           $mongo = DB::connection('mongodb')->getMongoDB();

           // Crear la colección 'personal_access_tokens'
           // Nota: MongoDB la crea automáticamente al insertar el primer documento,
           // pero aquí la creamos para poder definir índices de inmediato.
           $mongo->createCollection('personal_access_tokens');
   
           // Seleccionamos la colección para trabajar con ella
           $collection = $mongo->selectCollection('personal_access_tokens');
   
           // Creamos índices
           $collection->createIndex(['tokenable_id' => 1]);
           $collection->createIndex(['tokenable_type' => 1]);
           $collection->createIndex(['token' => 1], ['unique' => true]);
           $collection->createIndex(['last_used_at' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         // Para revertir, simplemente eliminamos la colección
         $db = DB::connection('mongodb')->getMongoDB();
         $db->dropCollection('personal_access_tokens');
    }
};
