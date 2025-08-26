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
        Schema::create('entradas_itens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('entrada_id')->constrained('entradas')->cascadeOnDelete();
            $table->foreignId('produto_id')->constrained('produtos');
            $table->string('codigo_barras_lote', 100)->nullable();
            $table->integer('qtd_caixas')->default(0);
            $table->integer('qtd_por_caixa')->default(1);
            $table->decimal('preco_compra_caixa', 12, 2);
            $table->decimal('preco_compra_unitario', 12, 2);
            $table->decimal('preco_venda_caixa', 12, 2);
            $table->decimal('preco_venda_unitario', 12, 2);
            $table->date('data_validade')->nullable();
            $table->decimal('subtotal', 12, 2)->storedAs('qtd_caixas * preco_compra_caixa');
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
        Schema::dropIfExists('entradas_itens');
    }
}
