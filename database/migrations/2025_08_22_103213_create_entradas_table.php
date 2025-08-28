<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntradasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('entradas', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('tipo_entrada_id');
            $table->unsignedBigInteger('fornecedor_id')->nullable();
            $table->string('fornecedor_ref', 100)->nullable();
            $table->string('numero_factura', 45)->nullable();

            $table->date('data_aquisicao');
            $table->date('data_factura');

            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('total_factura', 12, 2)->default(0);
            $table->decimal('total_desconto', 12, 2)->default(0);
            $table->decimal('total_iva', 12, 2)->default(0);
            $table->decimal('valor_remanescente', 12, 2)->default(0);

            $table->text('ficheiro_entrada')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('estado')->default(1);

            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            // Chaves estrangeiras
            $table->foreign('tipo_entrada_id')->references('id')->on('tipos_entradas');
            $table->foreign('fornecedor_id')->references('id')->on('fornecedores');
            $table->foreign('estado')->references('id')->on('estados');
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
