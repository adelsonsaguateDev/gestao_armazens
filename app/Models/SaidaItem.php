<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaidaItem extends Model
{
    use HasFactory;

    protected $table = 'saida_itens';

    protected $fillable = [
        'saida_id',
        'produto_id',
        'entrada_item_id',
        'quantidade',
        'preco_unitario',
        'preco_compra',
        'iva',
        'valor_iva',
        'custo',
        'desconto_percentual',
        'desconto_valor',
        'tipo_motivo',
        'motivo',
        'user_id',
        'activo',
    ];

    public function saida()
    {
        return $this->belongsTo(Saida::class, 'saida_id');
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
