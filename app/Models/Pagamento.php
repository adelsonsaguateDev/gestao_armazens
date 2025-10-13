<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'valor_pago',
        'numero_recibo',
        'data_pagamento',
        'numero',
        'tipo_pagamento_id',
        'cliente_id',
        'saida_id',
        'user_id',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tipoPagamento()
    {
        return $this->belongsTo(TipoPagamento::class, 'tipo_pagamento_id');
    }
}
