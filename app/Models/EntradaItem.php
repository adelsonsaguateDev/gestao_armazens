<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntradaItem extends Model
{
    use HasFactory;

    protected $table = 'entradas_itens';

    protected $fillable = [
        'entrada_id',
        'produto_id',
        'codigo_barras_lote',
        'qtd_caixas',
        'qtd_por_caixa',
        'preco_compra_caixa',
        'preco_compra_unitario',
        'preco_venda_caixa',
        'preco_venda_unitario',
        'data_validade',
        'subtotal',
        'user_id',
        'estado',
    ];

    public function entrada()
    {
        return $this->belongsTo(Entrada::class, 'entrada_id');
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function estadoObj()
    {
        return $this->belongsTo(Estado::class, 'estado');
    }
}