<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessoArquivo extends Model
{
    protected $table = 'tb_processos_arquivos';
    protected $primaryKey = 'id_arquivo';
    protected $fillable = [
        'fk_id_processo',
        'nome_exibicao',
        'caminho_arquivo',
        'tipo_arquivo',
    ];

    public function processo()
    {
        return $this->belongsTo(ProcessoSeletivo::class, 'fk_id_processo', 'id_processo');
    }
}
