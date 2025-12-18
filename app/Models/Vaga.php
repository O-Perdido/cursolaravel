<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vaga extends Model
{
    protected $table = 'tb_vagas';
    protected $primaryKey = 'id_vaga';
    protected $fillable = [
        'numero_vaga',
        'atividades',
        'fk_id_supervisor',
        'data_inicio',
        'data_termino',
        'horario',
        'fk_id_local',
        'fk_id_empresa',
        'lotacao',
        'valor_bolsa',
        'valor_auxilio_transporte',
        'status',
        'fk_id_termo',
        'vinculo_tipo',
        'descricao',
        'publicada_em',
        'remunerada',
        'tipo_vaga',
        // Campos de estagiário vinculado à vaga (opcionais)
        'tem_estagiario_definido',
        'nome_estagiario',
        'contato_whatsapp',
        'contato_email',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'fk_id_empresa', 'id_empresa');
    }

    public function local()
    {
        return $this->belongsTo(Local::class, 'fk_id_local', 'id_local');
    }

    public function termo()
    {
        return $this->belongsTo(Termo::class, 'fk_id_termo', 'id_termo');
    }

    public function supervisor()
    {
        return $this->belongsTo(Supervisor::class, 'fk_id_supervisor', 'id_supervisor');
    }

    // Gera número sequencial por empresa/ano
    public static function gerarNumeroVaga($empresaId)
    {
        $ano = date('Y');
        $ultimo = self::where('fk_id_empresa', $empresaId)
            ->whereYear('created_at', $ano)
            ->orderByDesc('id_vaga')
            ->first();
        $seq = $ultimo ? intval(explode('-', $ultimo->numero_vaga)[1]) + 1 : 1;
        return sprintf('%s-%03d', $ano, $seq);
    }
}
