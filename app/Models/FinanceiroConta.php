<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceiroConta extends Model
{
    use HasFactory;

    protected $table = 'tb_financeiro_contas';
    protected $primaryKey = 'id_financeiro_conta';

    protected $fillable = [
        'tipo_conta',
        'nome_conta',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function lancamentos()
    {
        return $this->hasMany(FinanceiroLancamento::class, 'fk_id_financeiro_conta', 'id_financeiro_conta');
    }

    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeDoTipo($query, string $tipo)
    {
        return $query->where('tipo_conta', $tipo);
    }

    public function scopeOrdenadas($query)
    {
        return $query->orderBy('nome_conta');
    }
}