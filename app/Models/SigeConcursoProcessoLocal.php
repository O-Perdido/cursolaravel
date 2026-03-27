<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SigeConcursoProcessoLocal extends Model
{
    use HasFactory;

    protected $table = 'sigeconcursos_tb_processo_locais';

    protected $primaryKey = 'id_processo_local';

    public $timestamps = false;

    protected $fillable = [
        'fk_id_processo',
        'fk_id_local_prova',
        'observacoes',
    ];

    public function processo()
    {
        return $this->belongsTo(SigeConcursoProcesso::class, 'fk_id_processo', 'id_processo');
    }

    public function localProva()
    {
        return $this->belongsTo(SigeConcursoLocalProva::class, 'fk_id_local_prova', 'id_local_prova');
    }

    public function inscricoesAtribuidas()
    {
        return $this->hasMany(SigeConcursoInscricaoLocal::class, 'fk_id_processo_local', 'id_processo_local');
    }
}