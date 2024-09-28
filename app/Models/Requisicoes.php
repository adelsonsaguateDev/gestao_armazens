<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requisicoes extends Model
{
    use HasFactory;

    protected $table = 'requisicoes';

    protected $fillable = ['produto_id','quantidade','estado','estado_requisicao','user_id']; 
    // Outras configurações e métodos, se necessário


    public function insert($produto_id, $quantidade){

        $requisicao = new Requisicoes();



        $requisicao->produto_id = $produto_id; 
        $requisicao->quantidade = $quantidade; 
        $requisicao->estado = '1'; 
        $requisicao->estado_requisicao = '1'; 
        $requisicao->user_id = auth()->user()->id; 
        $requisicao->save();

    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function produtos()
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }
}
