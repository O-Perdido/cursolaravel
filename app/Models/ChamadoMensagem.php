<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChamadoMensagem extends Model
{
    use HasFactory;

    protected $table = 'tb_chamados_mensagens';
    protected $primaryKey = 'id_chamado_mensagem';

    protected $fillable = [
        'fk_id_chamado',
        'fk_id_user_remetente',
        'remetente_nivel',
        'mensagem',
        'anexos',
        'lido_empresa_em',
        'lido_operador_em',
    ];

    protected $casts = [
        'anexos' => 'array',
        'lido_empresa_em' => 'datetime',
        'lido_operador_em' => 'datetime',
    ];

    public function chamado()
    {
        return $this->belongsTo(Chamado::class, 'fk_id_chamado', 'id_chamado');
    }

    public function remetente()
    {
        return $this->belongsTo(User::class, 'fk_id_user_remetente', 'id');
    }
}
