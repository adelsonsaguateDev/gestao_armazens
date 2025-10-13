<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIvaToEntradasItensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entradas_itens', function (Blueprint $table) {
            $table->decimal('iva', 8, 2)->default(0)->after('preco_venda_unitario');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entradas_itens', function (Blueprint $table) {
            $table->dropColumn('iva');
        });
    }
}
