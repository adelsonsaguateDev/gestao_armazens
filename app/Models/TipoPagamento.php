<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPagamento extends Model
{
    use HasFactory;

    protected $table = 'tipo_pagamentos';

    protected $fillable = [
        'designacao',
        'user_id',
        'is_active',
    ];
}