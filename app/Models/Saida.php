<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saida extends Model
{
    use HasFactory;

    protected $table = 'saidas';

    protected $fillable = [
        'cliente_id',
        'tipo_saida_id',
        'data',
        'valor_total',
        'valor_total_iva',
        'valor_pago',
        'valor_remanescente',
        'desconto',
        'valor_entregue',
        'trocos',
        'tipo_pagamento_id',
        'numero',
        'numero_cotacao',
        'validade_cotacao',
        'slip',
        'numero_factura',
        'estado_pagamento',
        'activo',
        'user_id',
    ];

    public function tipoSaida()
    {
        return $this->belongsTo(TipoSaida::class, 'tipo_saida_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function estadoObj()
    {
        return $this->belongsTo(Estado::class, 'activo');
    }

    public function itens()
    {
        return $this->hasMany(SaidaItem::class, 'saida_id');
    }
}
