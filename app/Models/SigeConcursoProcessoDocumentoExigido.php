<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SigeConcursoProcessoDocumentoExigido extends Model
{
    use HasFactory;

    protected $table = 'sigeconcursos_tb_processo_documentos_exigidos';

    protected $primaryKey = 'id_documento_exigido';

    public $timestamps = false;

    protected $fillable = [
        'fk_id_processo',
        'titulo',
        'descricao',
        'obrigatorio',
        'ordem_exibicao',
    ];

    protected function casts(): array
    {
        return [
            'obrigatorio' => 'boolean',
            'ordem_exibicao' => 'integer',
        ];
    }

    public function processo()
    {
        return $this->belongsTo(SigeConcursoProcesso::class, 'fk_id_processo', 'id_processo');
    }

    public function documentosInscricao()
    {
        return $this->hasMany(SigeConcursoInscricaoDocumento::class, 'fk_id_documento_exigido', 'id_documento_exigido');
    }
}