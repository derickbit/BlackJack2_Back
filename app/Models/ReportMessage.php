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
    public function getImagemUrlAttribute(): ?string
    {
        if ($this->imagem) {
            $disk = config('filesystems.default');

            if ($disk === 's3') {
                // Para S3 (produção) - usa a URL base configurada
                $bucket = config('filesystems.disks.s3.bucket');
                $region = config('filesystems.disks.s3.region');
                return "https://{$bucket}.s3.{$region}.amazonaws.com/{$this->imagem}";
            } else {
                // Para desenvolvimento local
                return asset('storage/' . $this->imagem);
            }
        }
        return null;
    }
}
