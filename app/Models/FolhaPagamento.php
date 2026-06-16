<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FolhaPagamento extends Model
{

    //Desabilitar timestamp
    public $timestamps = false;

    protected $table = 'tb_folhas_pagamento';

    protected $primaryKey = 'id_folha_pagamento';

    protected $fillable = [
        'numero_folha',
        'data_folha',
        'vencimento_folha',
        'ano_referencia',
        'mes_referencia',
        'fk_id_empresa',
        'fk_id_local',
        'total_bolsa_mes',
        'total_auxilio_transporte_mes',
        'total_recesso',
        'total_taxa_adm',
        'total_folha',
        'tipo_calculo_auxilio_transporte',
        'tipo_calculo_recesso',
        'dias_uteis',
        'notaas_invoice_id',
        'notaas_status',
        'notaas_pdf_url',
        'notaas_xml_url',
        'notaas_error_message',
        'notaas_emitted_at',
    ];

    //Mes referencia formatado por extenso
    public function getMesReferenciaFormatado()
    {
        $meses = [
            1 => 'Janeiro',
            2 => 'Fevereiro',
            3 => 'Março',
            4 => 'Abril',
            5 => 'Maio',
            6 => 'Junho',
            7 => 'Julho',
            8 => 'Agosto',
            9 => 'Setembro',
            10 => 'Outubro',
            11 => 'Novembro',
            12 => 'Dezembro'
        ];

        return $meses[$this->mes_referencia] ?? null;
    }


    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'fk_id_empresa');
    }

    public function local()
    {
        return $this->belongsTo(Local::class, 'fk_id_local', 'id_local');
    }

    public function folhasTermos()
    {
        return $this->hasMany(FolhasTermos::class, 'fk_id_folha', 'id_folha_pagamento');
    }

    public function notaFiscal()
    {
        return $this->hasOne(NotaasInvoice::class, 'fk_id_folha', 'id_folha_pagamento');
    }
}
