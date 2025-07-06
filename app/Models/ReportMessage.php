<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage; // <<< 1. ADICIONE O IMPORT AQUI (SE NÃO EXISTIR)

class ReportMessage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'report_id',
        'user_id',
        'mensagem',
        'imagem', // Este é o nome da coluna no banco que guarda o caminho relativo da imagem
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [ // <<< 2. ADICIONE OU MODIFIQUE ESTA PROPRIEDADE
        'imagem_url',
    ];

    /**
     * Get the user that owns the message.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the report that the message belongs to.
     */
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Get the full URL for the message's image.
     *
     * @return string|null
     */
    public function getImagemUrlAttribute(): ?string // <<< 3. ADICIONE ESTE MÉTODO ACCESSOR
    {
        if ($this->imagem) {
            // 'public' é o nome do disco configurado em config/filesystems.php
            // que geralmente aponta para storage/app/public e é acessível via
            // o link simbólico criado por `php artisan storage:link`.
            // Certifique-se que APP_URL no seu .env (e nas config vars do Heroku)
            // está correto (ex: https://seu-dominio.com) para que a URL seja gerada corretamente.
            return Storage::disk('s3')->url($this->imagem);
        }
        return null;
    }
}
