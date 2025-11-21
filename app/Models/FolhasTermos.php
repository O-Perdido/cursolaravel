<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FolhasTermos extends Model
{
    protected $table = 'tb_folhas_termos';

    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'fk_id_termo',
        'fk_id_folha',
        'dias_trabalhados',
        'valor_bolsa',
        'valor_bolsa_mes',
        'valor_auxilio_transporte',
        'valor_auxilio_transporte_mes',
        'valor_recesso',
        'taxa_adm',
        'descontos',
        'total',
    ];

    public function termo()
    {
        return $this->belongsTo(Termo::class, 'fk_id_termo', 'id_termo');
    }

    public function folhaPagamento()
    {
        return $this->belongsTo(FolhaPagamento::class, 'fk_id_folha', 'id_folha_pagamento');
    }


}
