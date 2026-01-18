<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessoSeletivo extends Model
{
    protected $table = 'tb_processos_seletivos';
    protected $primaryKey = 'id_processo';
    protected $fillable = [
        'numero_processo',
        'titulo',
        'fk_id_empresa',
        'status',
        'data_abertura',
        'data_fechamento_inscricoes',
        'descricao_fases',
        'cursos_destino',
        'requisitos',
        'observacoes',
        'aviso_inscricao',
    ];

    protected $casts = [
        'cursos_destino' => 'array',
        'data_abertura' => 'datetime',
        'data_fechamento_inscricoes' => 'datetime',
    ];

    // Relações
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'fk_id_empresa', 'id_empresa');
    }

    public function arquivos()
    {
        return $this->hasMany(ProcessoArquivo::class, 'fk_id_processo', 'id_processo');
    }

    public function inscricoes()
    {
        return $this->hasMany(InscricaoProcesso::class, 'fk_id_processo', 'id_processo');
    }

    public function resultados()
    {
        return $this->hasMany(ResultadoProcesso::class, 'fk_id_processo', 'id_processo');
    }

    // Helpers
    public function inscricoesCount()
    {
        return $this->inscricoes()->count();
    }

    public function estaEstaBerto()
    {
        return in_array($this->status, ['aberto', 'inscricoes']);
    }

    public function periodiInscricoesAberto()
    {
        $agora = now();
        if ($this->data_abertura && $this->data_fechamento_inscricoes) {
            return $agora >= $this->data_abertura && $agora <= $this->data_fechamento_inscricoes;
        }
        return false;
    }

    // Gera número sequencial por empresa/ano
    public static function gerarNumeroProcesso($empresaId)
    {
        $ano = date('Y');
        $lastSeq = self::where('fk_id_empresa', $empresaId)
            ->whereYear('created_at', $ano)
            ->select(\Illuminate\Support\Facades\DB::raw("MAX(CAST(SUBSTRING_INDEX(numero_processo,'-',-1) AS UNSIGNED)) as max_seq"))
            ->value('max_seq');
        $seq = ($lastSeq ? intval($lastSeq) : 0) + 1;
        return sprintf('%s-%04d', $ano, $seq);
    }
}
