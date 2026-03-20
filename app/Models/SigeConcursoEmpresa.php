<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SigeConcursoEmpresa extends Model
{
    use HasFactory;

    protected $table = 'sigeconcursos_tb_empresas';

    protected $primaryKey = 'id_empresa';

    public $timestamps = false;

    protected $fillable = [
        'nome_razao_social',
        'numero_cnpj',
        'numero_telefone',
        'numero_celular',
        'email',
        'numero_cep',
        'endereco',
        'numero_endereco',
        'complemento_endereco',
        'bairro',
        'fk_id_cidade',
        'nome_representante',
        'cargo_representante',
        'cpf_representante',
        'dados_bancarios',
    ];

    public function cidade()
    {
        return $this->belongsTo(Cidade::class, 'fk_id_cidade', 'id_cidade');
    }
}