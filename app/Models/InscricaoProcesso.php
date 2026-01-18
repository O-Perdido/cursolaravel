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
    ];

    public function processo()
    {
        return $this->belongsTo(ProcessoSeletivo::class, 'fk_id_processo', 'id_processo');
    }

    public function estagiario()
    {
        return $this->belongsTo(Estagiario::class, 'fk_id_estagiario', 'id_estagiario');
    }
}
