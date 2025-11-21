<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Representante extends Model
{
    use HasFactory;

    protected $table = 'tb_representantes';
    protected $primaryKey = 'id_representante';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'nome',
        'cargo',
        'cpf',
        'email',
        'representavel_type',
        'representavel_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento polimórfico: pode pertencer a Escola ou Empresa
     */
    public function representavel()
    {
        return $this->morphTo();
    }

    /**
     * Accessor: retorna tipo legível
     */
    public function getTipoVinculoAttribute()
    {
        return match($this->representavel_type) {
            'App\\Models\\Escola' => 'Instituição de Ensino',
            'App\\Models\\Empresa' => 'Unidade Concedente',
            default => 'Desconhecido',
        };
    }

    /**
     * Validação customizada (usar em FormRequest ou Controller)
     */
    public static function rules($id = null)
    {
        return [
            'nome' => 'required|string|max:255',
            'cargo' => 'required|string|max:100',
            'cpf' => 'nullable|string|size:14',
            'email' => 'required|email|max:255',
            'representavel_type' => 'required|in:App\\Models\\Escola,App\\Models\\Empresa',
            'representavel_id' => 'required|integer|exists:' . self::getTableFromType('{{representavel_type}}') . ',id',
        ];
    }

    /**
     * Helper para pegar nome da tabela baseado no tipo
     */
    protected static function getTableFromType($type)
    {
        return match($type) {
            'App\\Models\\Escola' => 'tb_escolas',
            'App\\Models\\Empresa' => 'tb_empresas',
            default => '',
        };
    }
}
