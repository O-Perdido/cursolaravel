<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SigeConcursoInscricaoLocal extends Model
{
    protected $table = 'sigeconcursos_tb_inscricao_locais';

    protected $primaryKey = 'id_inscricao_local';

    public $timestamps = false;

    protected $fillable = [
        'fk_id_inscricao',
        'fk_id_processo_local',
    ];

    public function inscricao()
    {
        return $this->belongsTo(SigeConcursoInscricao::class, 'fk_id_inscricao', 'id_inscricao');
    }

    public function processoLocal()
    {
        return $this->belongsTo(SigeConcursoProcessoLocal::class, 'fk_id_processo_local', 'id_processo_local');
    }
}
