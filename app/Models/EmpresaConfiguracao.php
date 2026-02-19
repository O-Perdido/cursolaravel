<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpresaConfiguracao extends Model
{
    use HasFactory;

    protected $table = 'tb_empresa_configuracoes';
    protected $primaryKey = 'id_empresa_configuracao';

    protected $fillable = [
        'fk_id_empresa',
        'chave',
        'valor',
        'descricao',
        'tipo',
    ];

    /**
     * Relação com Empresa
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'fk_id_empresa', 'id_empresa');
    }

    /**
     * Obtém o valor de uma configuração específica da empresa
     */
    public static function obterPorEmpresa($idEmpresa, $chave, $valorPadrao = null)
    {
        $config = self::where('fk_id_empresa', $idEmpresa)
            ->where('chave', $chave)
            ->first();

        if (!$config) {
            return $valorPadrao;
        }

        // Converte o valor baseado no tipo
        return match($config->tipo) {
            'numero' => (int) $config->valor,
            'decimal' => (float) $config->valor,
            'boolean' => filter_var($config->valor, FILTER_VALIDATE_BOOLEAN),
            default => $config->valor,
        };
    }

    /**
     * Define o valor de uma configuração para uma empresa específica
     */
    public static function definirPorEmpresa($idEmpresa, $chave, $valor, $descricao = null, $tipo = 'texto')
    {
        return self::updateOrCreate(
            [
                'fk_id_empresa' => $idEmpresa,
                'chave' => $chave,
            ],
            [
                'valor' => $valor,
                'descricao' => $descricao,
                'tipo' => $tipo,
            ]
        );
    }

    /**
     * Remove uma configuração específica da empresa (volta a usar a global)
     */
    public static function removerPorEmpresa($idEmpresa, $chave)
    {
        return self::where('fk_id_empresa', $idEmpresa)
            ->where('chave', $chave)
            ->delete();
    }

    /**
     * Obtém todas as configurações específicas de uma empresa
     */
    public static function obterTodasPorEmpresa($idEmpresa)
    {
        return self::where('fk_id_empresa', $idEmpresa)
            ->orderBy('chave')
            ->get();
    }
}
