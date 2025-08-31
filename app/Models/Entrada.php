<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrada extends Model
{
    use HasFactory;

    protected $table = 'entradas';

    protected $fillable = [
        'tipo_entrada_id',
        'fornecedor_id',
        'fornecedor_ref',
        'numero_factura',
        'data_aquisicao',
        'data_factura',
        'total_factura',
        'total_desconto',
        'total_iva',
        'valor_remanescente',
        'ficheiro_entrada',
        'user_id',
        'estado',
    ];

    public function tipoEntrada()
    {
        return $this->belongsTo(TipoEntrada::class, 'tipo_entrada_id');
    }

    public function fornecedor()
    {
        return $this->belongsTo(Fornecedor::class, 'fornecedor_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function estadoObj()
    {
        return $this->belongsTo(Estado::class, 'estado');
    }

    public function itens()
    {
        return $this->hasMany(EntradaItem::class, 'entrada_id');
    }
}