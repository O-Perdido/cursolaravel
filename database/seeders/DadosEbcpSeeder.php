<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DadosEbcpSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tb_dados_ebcp')->updateOrInsert(
            ['id_ebcp' => 1],
            [
                'nome_ebcp' => 'EBCP CONSULTORIA LTDA',
                'endereço_ebcp' => 'RUA WENCESLAU BRAZ 332, VILA MOEMA - TUBARÃO - SC',
                'cep_ebcp' => '88705-070',
                'email_ebcp' => 'contato@ebcpconsultoria.com.br',
                'contato_ebcp' => '(48) 9 9146-8761',
                'cnpj_ebcp' => '41.813.282/0001-23',
                'nome_representante' => 'MOACIR AGUIAR',
            ]
        );
    }
}
