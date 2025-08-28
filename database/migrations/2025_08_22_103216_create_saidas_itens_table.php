<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaidasItensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('saidas_itens', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('saida_id')->constrained('saidas')->cascadeOnDelete();
            $table->foreignId('entrada_item_id')->constrained('entradas_itens');
            $table->foreignId('produto_id')->constrained('produtos');
            $table->integer('qtd_caixas')->default(0);
            $table->integer('qtd_unidades')->default(0);
            $table->decimal('preco_venda_unitario', 12, 2);
            $table->decimal('preco_venda_caixa', 12, 2)->nullable();
            $table->decimal('subtotal', 12, 2)->storedAs('(qtd_caixas * COALESCE(preco_venda_caixa,0)) + (qtd_unidades * preco_venda_unitario)');
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
        Schema::dropIfExists('saidas_itens');
    }
}
