<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Partida extends Model
{
    use hasFactory;
    protected $table = 'partidas';
    protected $primaryKey = 'codpartida';

    protected $fillable = ['jogador','jogo', 'pontuacao'];

    public function jogador1()
    {
        return $this->belongsTo(User::class, 'jogador');
    }




}
