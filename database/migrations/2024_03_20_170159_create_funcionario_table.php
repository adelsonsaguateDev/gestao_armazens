<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFuncionarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('funcionario', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20);
            $table->string('nome', 100);
            $table->string('apelido', 50);
            $table->string('nrbi', 20);
            $table->text('morada');
            $table->string('telefone', 20);
            $table->date('data_nascimento');
            $table->string('email', 255);
            $table->string('genero', 20);
            $table->string('estado_civil', 20);
            $table->unsignedBigInteger('estado');
            $table->unsignedBigInteger('user_id');
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
        Schema::dropIfExists('funcionario');
    }
}
