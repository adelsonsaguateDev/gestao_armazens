<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    protected $table = 'produtos';

    // Assuming 'unidade' column is replaced by 'unidade_id'
    protected $fillable = ['nome', 'descricao', 'codigo_barras', 'stock_minimo', 'estado', 'user_id', 'unidade_id'];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function requisicoes()
    {
        // Correcting the relationship to point to Requisicoes model
        return $this->hasMany(Requisicoes::class, 'produto_id');
    }

    public function unidade()
    {
        // Relationship to Unidade model
        return $this->belongsTo(Unidade::class, 'unidade_id');
    }
}
