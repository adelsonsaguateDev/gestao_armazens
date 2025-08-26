<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTiposEntradasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produtos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('codigo_barras', 100)->nullable();
            $table->string('nome', 150);
            $table->text('descricao')->nullable();
            $table->string('unidade', 20);
            $table->integer('stock_minimo')->default(0);
            $table->string('imagem', 255)->nullable();
            $table->foreignId('user_id')->nullable();
            $table->foreignId('estado')->default(1)->constrained('estados');
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
        Schema::dropIfExists('produtos');
    }
}
