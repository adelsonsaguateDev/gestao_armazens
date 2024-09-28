<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';
   
    protected $fillable = [
        'name', 'username', 'email', 'contacto', 'password', 'estado'
    ];

 
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function permissoes()
    {
        return $this->belongsToMany(Permissao::class);
    }


    public function hasPermissao($permissao)
    {

        if ($this->permissoes()->where('nome', $permissao)->first()) {

            return true;
        }
        return false;
    }

    public function hasAnyPermissao($permissao)
    {

        if ($this->permissoes()->whereIn('nome', $permissao)->first()) {
            return true;
        }
        return false;
    }

    public function historico()
    {
        return $this->hasMany(Historico::class, 'user_id');
    }

    public function produtos()
    {
        return $this->hasMany(Produto::class, 'user_id');
    }

    public function requisicoes()
    {
        return $this->hasMany(Requisicoes::class, 'user_id');
    }
}
