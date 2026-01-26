<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InscricaoProcesso extends Model
{
    protected $table = 'tb_inscricoes_processo';
    protected $primaryKey = 'id_inscricao';
    protected $fillable = [
        'fk_id_processo',
        'fk_id_estagiario',
        'status_inscricao',
        'observacoes',
        'arquivo_inscricao',
        'numero_inscricao',
    ];

    public function processo()
    {
        return $this->belongsTo(ProcessoSeletivo::class, 'fk_id_processo', 'id_processo');
    }

    public function estagiario()
    {
        return $this->belongsTo(Estagiario::class, 'fk_id_estagiario', 'id_estagiario');
    }

    // Gera número de inscrição no formato: AAAA-NNNN-SSSS
    // Ex: 2026-0001-0001 (ano-num_processo-seq_inscricao)
    public static function gerarNumeroInscricao($fk_id_processo)
    {
        $ano = date('Y');
        
        // Obter número do processo (com padding para 4 dígitos)
        $processo = ProcessoSeletivo::find($fk_id_processo);
        if (!$processo) {
            return null;
        }
        
        // Extrair número sequencial do processo (ex: "2026-0001" -> 0001)
        $numProcesso = str_pad(
            (int) substr($processo->numero_processo, -4),
            4,
            '0',
            STR_PAD_LEFT
        );
        
        // Contar inscrições neste processo para gerar sequencial
        $proximoSeq = self::where('fk_id_processo', $fk_id_processo)
            ->lockForUpdate()
            ->count() + 1;
        
        $numSeq = str_pad($proximoSeq, 4, '0', STR_PAD_LEFT);
        
        return "{$ano}-{$numProcesso}-{$numSeq}";
    }
}
