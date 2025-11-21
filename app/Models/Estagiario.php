<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Cidade;
use App\Models\Estado;

class Estagiario extends Model
{
    use HasFactory;

    // Defina a tabela associada ao modelo, caso o nome da tabela não siga a convenção do Laravel
    protected $table = 'tb_estagiarios';

    protected $primaryKey = 'id_estagiario';  // Altere para o nome correto da coluna

    // Se a tabela não usar os campos de timestamp "created_at" e "updated_at", defina a propriedade $timestamps como false
    public $timestamps = false;

    // Atributos que são atribuíveis em massa (protegendo contra mass assignment)
    protected $fillable = [
        'nome_estagiario',
        'numero_cpf',
        'data_nascimento',
        'numero_telefone',
        'numero_celular',
        'email',
        'numero_cep',
        'endereco',
        'numero_endereco',
        'complemento_endereco',
        'bairro',
        'fk_id_cidade',
        'instituicao_ensino', // Novo campo
        'curso',
        'nivel_curso',
        'area_de_estagio',
        'nome_mae',
        'foto_documento',
        'comprovante_residencia',
        'comprovante_escolar',
        'numero_pis',
        'tipo_chave_pix',
        'chave_pix',
    ];


    // Caso os campos de data precisem ser tratados de forma específica (caso o formato de data não seja o padrão)
    protected $dates = ['data_nascimento'];

    // Caso precise realizar algum relacionamento com outras tabelas (como cidade e escola)

    // Relacionamento com a Cidade
    public function cidade()
    {
        return $this->belongsTo(Cidade::class, 'fk_id_cidade');
    }

    // Exemplo de mutator para tratar o campo de CPF (remover caracteres não numéricos)
    public function setNumeroCpfAttribute($value)
    {
        $this->attributes['numero_cpf'] = preg_replace('/\D/', '', $value);  // Remove caracteres não numéricos
    }

    // Exemplo de accessor para formatar o CPF de forma mais legível
    public function getNumeroCpfAttribute($value)
    {
        return substr($value, 0, 3) . '.' . substr($value, 3, 3) . '.' . substr($value, 6, 3) . '-' . substr($value, 9, 2);
    }

    // Método para formatar a data de nascimento
    public function getDataNascimentoAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('d/m/Y');
    }

    // Relacionamento com o Estado
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'fk_id_estado');
    }

    public function termo()
    {
        return $this->hasMany(Termo::class, 'fk_id_estagiario');
    }
}
