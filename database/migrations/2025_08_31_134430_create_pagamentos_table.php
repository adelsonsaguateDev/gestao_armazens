<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto_increment (id)
            $table->double('valor_pago', 12, 2);
            $table->string('numero_recibo', 45);
            $table->dateTime('data_pagamento');
            $table->string('numero', 45)->nullable();

            // Relacionamentos (FKs)
            $table->unsignedBigInteger('tipo_pagamento_id');
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('saida_id');
            $table->unsignedBigInteger('user_id');

            // Timestamps personalizados
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();

            // Indexes
            $table->index('cliente_id');
            $table->index('saida_id');
            $table->index('user_id');
            $table->index('tipo_pagamento_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};
