<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SigeConcursoInterCobrancaLog extends Model
{
    use HasFactory;

    protected $table = 'sigeconcursos_tb_inter_cobranca_logs';

    protected $primaryKey = 'id_log';

    protected $fillable = [
        'fk_id_inscricao',
        'codigo_solicitacao',
        'tipo_evento',
        'sucesso',
        'status_http',
        'mensagem',
        'payload_request',
        'payload_response',
    ];

    protected function casts(): array
    {
        return [
            'sucesso' => 'boolean',
        ];
    }

    public function inscricao()
    {
        return $this->belongsTo(SigeConcursoInscricao::class, 'fk_id_inscricao', 'id_inscricao');
    }
}
