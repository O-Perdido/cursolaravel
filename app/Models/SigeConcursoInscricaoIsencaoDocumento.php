<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SigeConcursoInscricaoIsencaoDocumento extends Model
{
    use HasFactory;

    protected $table = 'sigeconcursos_tb_inscricao_isencao_documentos';

    protected $primaryKey = 'id_inscricao_isencao_documento';

    protected $fillable = [
        'fk_id_inscricao',
        'nome_documento',
        'caminho_arquivo',
    ];

    public function inscricao()
    {
        return $this->belongsTo(SigeConcursoInscricao::class, 'fk_id_inscricao', 'id_inscricao');
    }
}