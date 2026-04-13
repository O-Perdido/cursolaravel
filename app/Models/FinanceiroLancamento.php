<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceiroLancamento extends Model
{
    use HasFactory;

    protected $table = 'tb_financeiro_lancamentos';
    protected $primaryKey = 'id_financeiro_lancamento';

    protected $fillable = [
        'fk_id_financeiro_conta',
        'fk_id_usuario_criacao',
        'fk_id_usuario_atualizacao',
        'ano_referencia',
        'mes_referencia',
        'valor',
        'observacao',
    ];

    protected $casts = [
        'ano_referencia' => 'integer',
        'mes_referencia' => 'integer',
        'valor' => 'decimal:2',
    ];

    public function conta()
    {
        return $this->belongsTo(FinanceiroConta::class, 'fk_id_financeiro_conta', 'id_financeiro_conta');
    }

    public function usuarioCriacao()
    {
        return $this->belongsTo(User::class, 'fk_id_usuario_criacao', 'id');
    }

    public function usuarioAtualizacao()
    {
        return $this->belongsTo(User::class, 'fk_id_usuario_atualizacao', 'id');
    }
}