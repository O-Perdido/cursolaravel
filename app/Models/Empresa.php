<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    protected $table = 'tb_empresas'; // Nome da tabela no banco de dados
    protected $primaryKey = 'id_empresa'; // Nome da chave primária
    public $timestamps = false; // Se você desativou os timestamps

    protected $fillable = [
        'nome_empresa',
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
        'tipo_taxa',
        'taxa_fixa',
        'taxa_percentual'
    ];

    public function cidade()
    {
        return $this->belongsTo(Cidade::class, 'fk_id_cidade', 'id_cidade');
    }

    public function termo()
    {
        return $this->hasMany(Termo::class, 'fk_id_empresa');
    }

    public function supervisor()
    {
        return $this->hasMany(Supervisor::class, 'fk_id_empresa');
    }

    public function usuario()
    {
        return $this->hasMany(User::class, 'fk_id_empresa');
    }

    public function folhaPagamento()
    {
        return $this->hasMany(FolhaPagamento::class, 'fk_id_empresa');
    }

    public function locais()
    {
        return $this->hasMany(Local::class, 'fk_id_empresa', 'id_empresa');
    }

    /**
     * Relacionamento polimórfico com Representantes
     */
    public function representantes()
    {
        return $this->morphMany(Representante::class, 'representavel');
    }
}
