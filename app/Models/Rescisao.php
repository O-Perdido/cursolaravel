<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rescisao extends Model
{
    use HasFactory;

    // Especifica o nome da tabela associada ao model
    protected $table = 'tb_rescisao';

    protected $primaryKey = 'id_rescisao';

    // Permite a atribuição em massa para os campos especificados
    protected $fillable = [
        'fk_id_termo',
        'data_rescisao',
        'motivo',
        'zapsign_doc_token',
        'zapsign_status',
        'zapsign_enviado_em',
    ];

    public function termo()
    {
        return $this->belongsTo(Termo::class, 'fk_id_termo');
    }
}