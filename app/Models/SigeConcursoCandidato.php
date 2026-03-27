<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SigeConcursoCandidato extends Model
{
    use HasFactory;

    protected $table = 'sigeconcursos_tb_candidatos';

    protected $primaryKey = 'id_candidato';

    public $timestamps = false;

    protected $fillable = [
        'nome_completo',
        'numero_cpf',
        'data_nascimento',
        'sexo',
        'email',
        'numero_rg',
        'orgao_expedidor_rg',
        'uf_rg',
        'nome_mae',
        'nacionalidade',
        'naturalidade_cidade',
        'naturalidade_estado',
        'canhoto',
        'numero_cep',
        'endereco',
        'numero_endereco',
        'complemento_endereco',
        'bairro',
        'fk_id_cidade',
        'numero_telefone',
        'numero_celular',
    ];

    protected function casts(): array
    {
        return [
            'data_nascimento' => 'date',
        ];
    }

    public function cidade()
    {
        return $this->belongsTo(Cidade::class, 'fk_id_cidade', 'id_cidade');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'fk_id_candidato', 'id_candidato');
    }

    public function inscricoes()
    {
        return $this->hasMany(SigeConcursoInscricao::class, 'fk_id_candidato', 'id_candidato');
    }
}