<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotaasInvoice extends Model
{
    protected $table = 'tb_notas_fiscais';

    protected $fillable = [
        'fk_id_folha',
        'fk_id_empresa',
        'notaas_invoice_id',
        'notaas_status',
        'notaas_pdf_url',
        'notaas_xml_url',
        'notaas_error_message',
        'notaas_emitted_at',
        'tomador_nome',
        'tomador_cnpj',
        'tomador_email',
        'tomador_telefone',
        'tomador_endereco',
        'tomador_numero',
        'tomador_bairro',
        'tomador_cidade',
        'tomador_uf',
        'tomador_cep',
        'valor',
        'descricao',
        'codigo_servico',
        'aliquota_iss',
        'iss_retido',
        'competencia',
        'referencia'
    ];

    protected $casts = [
        'notaas_emitted_at' => 'datetime',
        'valor' => 'decimal:2',
        'aliquota_iss' => 'decimal:2',
        'iss_retido' => 'boolean',
    ];

    public function folhaPagamento()
    {
        return $this->belongsTo(FolhaPagamento::class, 'fk_id_folha', 'id_folha_pagamento');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'fk_id_empresa', 'id_empresa');
    }
}
