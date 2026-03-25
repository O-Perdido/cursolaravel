<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SigeConcursoProcessoArquivo extends Model
{
    use HasFactory;

    protected $table = 'sigeconcursos_tb_processo_arquivos';

    protected $primaryKey = 'id_arquivo';

    public $timestamps = false;

    protected $fillable = [
        'fk_id_processo',
        'nome_exibicao',
        'tipo_arquivo',
        'caminho_arquivo',
        'ordem_exibicao',
    ];

    public function processo()
    {
        return $this->belongsTo(SigeConcursoProcesso::class, 'fk_id_processo', 'id_processo');
    }
}