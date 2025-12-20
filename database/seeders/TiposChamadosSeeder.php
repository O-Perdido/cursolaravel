<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TipoChamado;

class TiposChamadosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            [
                'nome' => 'Rescisão de Contrato',
                'slug' => 'rescisao',
                'descricao' => 'Solicitar rescisão de contrato de estágio',
                'sistema' => true,
                'ativo' => true,
                'ordem' => 1,
            ],
            [
                'nome' => 'Alteração de Termo de Contrato',
                'slug' => 'alteracao',
                'descricao' => 'Solicitar alteração em termo de contrato vigente',
                'sistema' => true,
                'ativo' => true,
                'ordem' => 2,
            ],
            [
                'nome' => 'Outros',
                'slug' => 'outros',
                'descricao' => 'Outros assuntos não especificados',
                'sistema' => false,
                'ativo' => true,
                'ordem' => 99,
            ],
        ];

        foreach ($tipos as $tipo) {
            TipoChamado::create($tipo);
        }
    }
}
