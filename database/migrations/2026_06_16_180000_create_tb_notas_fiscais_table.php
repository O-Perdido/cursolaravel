<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_notas_fiscais', function (Blueprint $table) {
            $table->id();
            $table->integer('fk_id_folha')->nullable();
            $table->integer('fk_id_empresa')->nullable();
            $table->string('notaas_invoice_id')->nullable()->unique();
            $table->string('notaas_status')->default('queued');
            $table->text('notaas_pdf_url')->nullable();
            $table->text('notaas_xml_url')->nullable();
            $table->text('notaas_error_message')->nullable();
            $table->timestamp('notaas_emitted_at')->nullable();
            
            // Dados do Tomador (snapshot para histórico e notas avulsas)
            $table->string('tomador_nome');
            $table->string('tomador_cnpj');
            $table->string('tomador_email')->nullable();
            $table->string('tomador_telefone')->nullable();
            $table->string('tomador_endereco')->nullable();
            $table->string('tomador_numero')->nullable();
            $table->string('tomador_bairro')->nullable();
            $table->string('tomador_cidade')->nullable();
            $table->string('tomador_uf')->nullable();
            $table->string('tomador_cep')->nullable();
            
            // Dados Financeiros e de Serviço
            $table->decimal('valor', 12, 2);
            $table->text('descricao');
            $table->string('codigo_servico')->nullable();
            $table->decimal('aliquota_iss', 4, 2)->default(6.00);
            $table->boolean('iss_retido')->default(false);
            
            $table->string('competencia')->nullable();
            $table->string('referencia')->nullable();
            
            $table->timestamps();

            $table->foreign('fk_id_folha')->references('id_folha_pagamento')->on('tb_folhas_pagamento')->onDelete('set null');
            $table->foreign('fk_id_empresa')->references('id_empresa')->on('tb_empresas')->onDelete('set null');
        });

        // Migrar dados existentes da tb_folhas_pagamento para tb_notas_fiscais
        $existing = DB::table('tb_folhas_pagamento')->whereNotNull('notaas_invoice_id')->get();
        foreach ($existing as $folha) {
            $empresa = DB::table('tb_empresas')->where('id_empresa', $folha->fk_id_empresa)->first();
            $cidade = null;
            $uf = null;
            if ($empresa) {
                $cidadeRow = DB::table('tb_cidade')->where('id_cidade', $empresa->fk_id_cidade)->first();
                if ($cidadeRow) {
                    $cidade = $cidadeRow->nm_cidade;
                    $estadoRow = DB::table('tb_estado')->where('id_estado', $cidadeRow->fk_id_estado)->first();
                    $uf = $estadoRow ? $estadoRow->uf_estado : null;
                }
            }

            DB::table('tb_notas_fiscais')->insert([
                'fk_id_folha' => $folha->id_folha_pagamento,
                'fk_id_empresa' => $folha->fk_id_empresa,
                'notaas_invoice_id' => $folha->notaas_invoice_id,
                'notaas_status' => $folha->notaas_status ?: 'queued',
                'notaas_pdf_url' => $folha->notaas_pdf_url,
                'notaas_xml_url' => $folha->notaas_xml_url,
                'notaas_error_message' => $folha->notaas_error_message,
                'notaas_emitted_at' => $folha->notaas_emitted_at,
                'tomador_nome' => $empresa ? $empresa->nome_empresa : 'Desconhecido',
                'tomador_cnpj' => $empresa ? preg_replace('/\D/', '', $empresa->numero_cnpj) : '00000000000000',
                'tomador_email' => $empresa ? $empresa->email : null,
                'tomador_telefone' => $empresa ? preg_replace('/\D/', '', $empresa->numero_telefone) : null,
                'tomador_endereco' => $empresa ? $empresa->endereco : null,
                'tomador_numero' => $empresa ? ($empresa->numero_endereco ?: 'S/N') : null,
                'tomador_bairro' => $empresa ? ($empresa->bairro ?: 'Centro') : null,
                'tomador_cidade' => $cidade,
                'tomador_uf' => $uf,
                'tomador_cep' => $empresa ? preg_replace('/\D/', '', $empresa->numero_cep) : null,
                'valor' => $folha->total_taxa_adm ?: 0.00,
                'descricao' => 'TAXA DE CONTRATAÇÃO E ADMINISTRAÇÃO DE CONTRATOS DE ESTÁGIOS',
                'aliquota_iss' => 6.00,
                'iss_retido' => false,
                'competencia' => sprintf('%04d-%02d', $folha->ano_referencia, $folha->mes_referencia),
                'referencia' => 'FOLHA-' . $folha->id_folha_pagamento,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_notas_fiscais');
    }
};
