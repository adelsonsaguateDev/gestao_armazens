<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->text('descricao');
            $table->string('endereco', 255);
            $table->integer('nuit');
            $table->string('contacto', 20);
            $table->string('contacto_alt', 20);
            $table->string('email', 255);
            $table->string('website', 255);
            $table->string('logo', 255);
            $table->string('logo_alt', 255);
            $table->string('cor', 20);
            $table->string('cor_geral', 20);
            $table->string('cor_primaria', 20);
            $table->string('cor_secundaria', 20);
            $table->string('cor_tercearia', 20);
            $table->string('cor_quaternaria', 20);
            $table->string('cor_texto', 20);
            $table->text('texto1');
            $table->text('texto2');
            $table->text('direitos');
            $table->text('desenvolvido_por');
            $table->text('manual_utilizador');
            $table->text('logo_dev');
            $table->text('nome_dev');
            $table->text('smtp');
            $table->integer('port');
            $table->text('sender_email');
            $table->text('sender_pass');
            $table->text('sender_name');
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
        Schema::dropIfExists('config');
    }
}
