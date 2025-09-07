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
}
