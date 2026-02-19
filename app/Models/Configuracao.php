<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracao extends Model
{
    use HasFactory;

    protected $table = 'configuracoes';
    protected $primaryKey = 'id_configuracao';

    protected $fillable = [
        'chave',
        'valor',
        'descricao',
        'tipo',
    ];

    /**
     * Obtém o valor de uma configuração pela chave
     */
    public static function obter($chave, $valorPadrao = null)
    {
        $config = self::where('chave', $chave)->first();
        
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
     * Define o valor de uma configuração
     */
    public static function definir($chave, $valor, $descricao = null, $tipo = 'texto')
    {
        return self::updateOrCreate(
            ['chave' => $chave],
            [
                'valor' => $valor,
                'descricao' => $descricao,
                'tipo' => $tipo,
            ]
        );
    }

    /**
     * Obtém o limite diário para remessas bancárias
     */
    public static function obterLimiteDiarioRemessa()
    {
        return self::obter('limite_diario_remessa', 50000.00);
    }

    /**
     * Obtém uma configuração com fallback automático:
     * 1. Tenta buscar a configuração específica da empresa
     * 2. Se não encontrar, usa a configuração global
     * 3. Se nem isso existir, usa o valor padrão
     * 
     * @param string $chave Chave da configuração
     * @param int|null $idEmpresa ID da empresa (optional)
     * @param mixed $valorPadrao Valor padrão se nada for encontrado
     * @return mixed
     */
    public static function obterComFallback($chave, $idEmpresa = null, $valorPadrao = null)
    {
        // Se foi informado um ID de empresa, tenta buscar a config específica primeiro
        if ($idEmpresa) {
            $configEmpresa = \App\Models\EmpresaConfiguracao::obterPorEmpresa($idEmpresa, $chave);
            if ($configEmpresa !== null) {
                return $configEmpresa;
            }
        }

        // Fallback: busca a configuração global
        return self::obter($chave, $valorPadrao);
    }
}
