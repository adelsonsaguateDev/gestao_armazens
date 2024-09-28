<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    protected $table = 'produtos';

    protected $fillable = ['nome','descricao','codigo','stock_minimo','estado','quantidade','user_id'];
    // Outras configurações e métodos, se necessário


    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function requisicoes()
    {
        return $this->hasMany(User::class, 'produto_id');
    }
}
