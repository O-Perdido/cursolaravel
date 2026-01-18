<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultadoProcesso extends Model
{
    protected $table = 'tb_resultados_processo';
    protected $primaryKey = 'id_resultado';
    protected $fillable = [
        'fk_id_processo',
        'numero_resultado',
        'arquivo_resultado',
    ];

    public function processo()
    {
        return $this->belongsTo(ProcessoSeletivo::class, 'fk_id_processo', 'id_processo');
    }
}
