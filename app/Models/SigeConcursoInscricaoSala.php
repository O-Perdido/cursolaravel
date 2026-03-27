<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SigeConcursoInscricaoSala extends Model
{
    protected $table = 'sigeconcursos_tb_inscricao_salas';

    protected $primaryKey = 'id_inscricao_sala';

    public $timestamps = false;

    protected $fillable = [
        'fk_id_inscricao',
        'fk_id_sala',
        'numero_assento',
    ];

    protected function casts(): array
    {
        return [
            'numero_assento' => 'integer',
        ];
    }

    public function inscricao()
    {
        return $this->belongsTo(SigeConcursoInscricao::class, 'fk_id_inscricao', 'id_inscricao');
    }

    public function sala()
    {
        return $this->belongsTo(SigeConcursoSala::class, 'fk_id_sala', 'id_sala');
    }
}
