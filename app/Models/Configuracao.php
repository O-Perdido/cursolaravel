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
}
