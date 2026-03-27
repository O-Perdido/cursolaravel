<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SigeConcursoInscricaoDocumento extends Model
{
    use HasFactory;

    protected $table = 'sigeconcursos_tb_inscricao_documentos';

    protected $primaryKey = 'id_inscricao_documento';

    protected $fillable = [
        'fk_id_inscricao',
        'fk_id_documento_exigido',
        'titulo_documento',
        'caminho_arquivo',
        'observacoes',
    ];

    public function inscricao()
    {
        return $this->belongsTo(SigeConcursoInscricao::class, 'fk_id_inscricao', 'id_inscricao');
    }

    public function documentoExigido()
    {
        return $this->belongsTo(SigeConcursoProcessoDocumentoExigido::class, 'fk_id_documento_exigido', 'id_documento_exigido');
    }
}