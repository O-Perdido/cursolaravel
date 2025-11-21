<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EBCP extends Model
{
    use HasFactory;

    protected $table = 'tb_dados_ebcp';

    protected $primaryKey = 'id_ebcp';

    public $timestamps = false;

    protected $fillable = [
        'nome_ebcp',
        'endereco_ebcp',
        'cep_ebcp',
        'email_ebcp',
        'contato_ebcp',
        'cnpj_ebcp',
        'nome_representante_ebcp'
    ];
}
