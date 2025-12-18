<?php

use App\Models\Empresa;
use App\Models\Supervisor;
use App\Models\Local;
use App\Models\Vaga;
use Illuminate\Support\Facades\Schema;

// Evita falha quando tabelas não existem no ambiente de teste

it('retorna vagas com supervisor no endpoint de empresa', function () {
    if (!Schema::hasTable('tb_local') || !Schema::hasTable('tb_vagas') || !Schema::hasTable('tb_supervisores') || !Schema::hasTable('tb_empresas')) {
        $this->markTestSkipped('Tabelas necessárias não existem no ambiente de teste.');
    }
    $empresa = Empresa::factory()->create();
    $supervisor = Supervisor::factory()->create([
        'fk_id_empresa' => $empresa->id_empresa,
    ]);

    $local = Local::factory()->create([
        'fk_id_empresa' => $empresa->id_empresa,
        'descricao' => 'Local Teste',
    ]);

    $vaga = Vaga::factory()->create([
        'fk_id_empresa' => $empresa->id_empresa,
        'fk_id_local' => $local->id_local,
        'fk_id_supervisor' => $supervisor->id_supervisor,
        'numero_vaga' => 'V123',
        'atividades' => 'Atividades de teste',
    ]);

    $response = $this->getJson('/api/vagas-por-empresa?empresa_id=' . $empresa->id_empresa);

    $response->assertOk();
    $response->assertJson(fn ($json) =>
        $json->where('0.id_vaga', $vaga->id_vaga)
             ->where('0.fk_id_supervisor', $supervisor->id_supervisor)
             ->where('0.supervisor.id_supervisor', $supervisor->id_supervisor)
             ->where('0.supervisor.fk_id_empresa', $empresa->id_empresa)
             ->etc()
    );
});
