<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Escola extends Model
{
    use HasFactory;

    // Nome da tabela (opcional, caso o nome da tabela seja diferente do plural do modelo)
    protected $table = 'tb_escolas';

    // Chave primária da tabela
    protected $primaryKey = 'id_escola';

    // Indica se a chave primária é autoincrementada
    public $incrementing = true;

    // Tipo da chave primária
    protected $keyType = 'int';

    // Ativa/desativa os timestamps (created_at e updated_at)
    public $timestamps = false;

    // Definir os campos que podem ser preenchidos em massa
    protected $fillable = [
        'nome_escola',
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
        'numero_apolice',
        'nome_seguradora',
        'nao_assina_zapsign',
        'orientacao_assinatura',
    ];

    // Definir os campos que devem ser ocultados ao serializar o modelo (opcional)
    protected $hidden = [
        'cpf_representante', // Exemplo
    ];

    // Cast dos campos para tipos específicos
    protected $casts = [
        'aceitacao_termos' => 'boolean',
        'nao_assina_zapsign' => 'boolean',
    ];

    // Relacionamento com a Cidade
    public function cidade()
    {
        return $this->belongsTo(Cidade::class, 'fk_id_cidade');
    }

    // Relacionamento com o Estado
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'fk_id_estado');
    }

    public function termo()
    {
        return $this->hasMany(Termo::class, 'fk_id_escola');
    }

    /**
     * Relacionamento polimórfico com Representantes
     */
    public function representantes()
    {
        return $this->morphMany(Representante::class, 'representavel');
    }
}