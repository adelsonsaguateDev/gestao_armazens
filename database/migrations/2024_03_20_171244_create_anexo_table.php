<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnexoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anexo', function (Blueprint $table) {
            $table->id();
            $table->string('tabela', 100);
            $table->integer('row_id');
            $table->unsignedBigInteger('tipo_documento_id');
            $table->text('descricao');
            $table->text('caminho');
            $table->text('nome_original');
            $table->text('novo_nome');
            $table->string('extensao', 10);
            $table->unsignedBigInteger('estado')->default(1);
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
        Schema::dropIfExists('anexo');
    }
}
