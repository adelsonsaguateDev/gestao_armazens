<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'nome',
        'contacto',
        'nuit',
        'endereco',
        'user_id',
        'estado',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function estadoObj()
    {
        return $this->belongsTo(Estado::class, 'estado');
    }

    public function saidas()
    {
        return $this->hasMany(Saida::class, 'cliente_id');
    }
}
