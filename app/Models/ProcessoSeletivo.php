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
        'icone_processo',
        'fk_id_empresa',
        'status',
        'data_abertura',
        'data_inicio_inscricoes',
        'data_fechamento_inscricoes',
        'descricao_fases',
        'fases',
        'cursos_destino',
        'vagas_por_nivel',
        'requisitos',
        'observacoes',
        'aviso_inscricao',
        'solicitar_upload_inscricao',
    ];

    protected $casts = [
        'cursos_destino' => 'array',
        'vagas_por_nivel' => 'array',
        'fases' => 'array',
        'data_abertura' => 'datetime',
        'data_inicio_inscricoes' => 'datetime',
        'data_fechamento_inscricoes' => 'datetime',
        'solicitar_upload_inscricao' => 'boolean',
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

    public function inicioInscricoes()
    {
        return $this->data_inicio_inscricoes ?? $this->data_abertura;
    }

    public function periodoInscricoesAberto(): bool
    {
        $agora = now();
        $inicio = $this->inicioInscricoes();
        $fim = $this->data_fechamento_inscricoes;

        if ($this->status !== 'inscricoes') {
            return false;
        }

        if ($inicio && $agora->lt($inicio)) {
            return false;
        }

        if ($fim && $agora->gt($fim)) {
            return false;
        }

        return true;
    }

    public function periodiInscricoesAberto(): bool
    {
        // Alias legado para compatibilidade
        return $this->periodoInscricoesAberto();
    }

    public function inscricoesEmBreve(): bool
    {
        $inicio = $this->inicioInscricoes();
        $agora = now();

        if (!$inicio) {
            return $this->status === 'aberto';
        }

        return $agora < $inicio;
    }

    public function inscricoesEncerradas(): bool
    {
        $fim = $this->data_fechamento_inscricoes;

        return $this->status === 'encerrado' || ($fim && now()->gt($fim));
    }

    /**
     * Retorna o status dinâmico do processo considerando prazos de inscrição
     * - Se status = "aberto" ou "finalizado" → retorna como está
     * - Se status = "inscricoes":
     *   - Antes da data de abertura → retorna "aberto"
     *   - Dentro do período → retorna "inscricoes"
     *   - Após a data de encerramento → retorna "encerrado"
     */
    public function getStatusDinamico(): string
    {
        // Status que não mudam dinamicamente
        if (in_array($this->status, ['aberto', 'finalizado'])) {
            return $this->status;
        }

        // Se status é "inscricoes", valida com as datas
        if ($this->status === 'inscricoes') {
            $agora = now();
            $inicio = $this->data_inicio_inscricoes ?? $this->data_abertura;
            $fim = $this->data_fechamento_inscricoes;

            // Antes da data de abertura
            if ($inicio && $agora->lt($inicio)) {
                return 'aberto';
            }

            // Após a data de encerramento
            if ($fim && $agora->gt($fim)) {
                return 'encerrado';
            }

            // Dentro do período de inscrições
            return 'inscricoes';
        }

        // Para qualquer outro status (como "encerrado"), retorna como está
        return $this->status;
    }

    // Gera número sequencial por empresa/ano
    public static function gerarNumeroProcesso()
    {
        $ano = date('Y');

        // Garante consistência mesmo com requisições concorrentes
        $lastSeq = self::whereYear('created_at', $ano)
            ->lockForUpdate()
            ->select(\Illuminate\Support\Facades\DB::raw("MAX(CAST(SUBSTRING_INDEX(numero_processo,'-',-1) AS UNSIGNED)) as max_seq"))
            ->value('max_seq');

        $seq = ($lastSeq ? intval($lastSeq) : 0) + 1;

        return sprintf('%s-%04d', $ano, $seq);
    }
}
