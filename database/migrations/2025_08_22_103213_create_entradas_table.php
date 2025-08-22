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
        Schema::create('entradas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('tipo_entrada_id')->constrained('tipos_entradas');
            $table->foreignId('fornecedor_id')->nullable()->constrained('fornecedores');
            $table->date('data_entrada');
            $table->boolean('imposto_aplicado')->default(false);
            $table->decimal('total', 12, 2)->default(0);
            $table->foreignId('user_id')->nullable();
            $table->foreignId('estado_id')->default(1)->constrained('estados');
            $table->timestamps();
        });
    }



    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entradas');
    }
}
