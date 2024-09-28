<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistritoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distrito', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->unsignedBigInteger('provincia_id'); // Adiciona a coluna provincia_id
            $table->tinyInteger('estado')->default(1); // Adiciona a coluna estado com valor padrão 1
            $table->foreign('provincia_id')
            ->references('id')
            ->on('provincia')
            ->onDelete('cascade') // Definir comportamento onDelete
            ->onUpdate('cascade'); // Definir comportamento onUpdate
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('distrito');
    }
}
