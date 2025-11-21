<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConcessaoRecesso extends Model
{
    use HasFactory;

    protected $table = 'tb_concessoes_recesso';
    protected $primaryKey = 'id_concessao';

    protected $fillable = [
        'fk_id_termo',
        'data_inicio_recesso',
        'data_fim_recesso',
        'total_dias',
        'data_concessao',
        'fk_id_usuario',
        'status',
        'motivo_exclusao',
        'data_exclusao',
        'fk_id_usuario_exclusao',
    ];

    protected $casts = [
        'data_inicio_recesso' => 'date',
        'data_fim_recesso' => 'date',
        'data_concessao' => 'datetime',
        'data_exclusao' => 'datetime',
    ];

    // Relacionamentos
    public function termo()
    {
        return $this->belongsTo(Termo::class, 'fk_id_termo', 'id_termo');
    }

    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'fk_id_usuario', 'id');
    }

    public function usuarioExclusao()
    {
        return $this->belongsTo(\App\Models\User::class, 'fk_id_usuario_exclusao', 'id');
    }

    // Scopes
    public function scopeAtivas($query)
    {
        return $query->where('status', 'ativo');
    }

    public function scopeExcluidas($query)
    {
        return $query->where('status', 'excluido');
    }
}
