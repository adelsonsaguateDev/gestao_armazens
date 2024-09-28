<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermanenciaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permanencia', function (Blueprint $table) {
            $table->id();
            $table->string('descricao', 100);
            $table->unsignedBigInteger('veiculo_id');
            $table->unsignedBigInteger('vaga_id');
            $table->timestamp('data_entrada');
            $table->timestamp('data_saida')->nullable(); // Change to nullable
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permanencia');
    }
}
