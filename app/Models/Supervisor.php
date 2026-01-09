<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supervisor extends Model
{
    use HasFactory;

    protected $table = 'tb_supervisores'; // Nome da tabela no banco de dados
    protected $primaryKey = 'id_supervisor'; // Nome da chave primária
    public $timestamps = false; // Se você desativou os timestamps

    protected $fillable = [
        'nome_supervisor',
        'fk_id_empresa',
        'area_formacao',
        'tempo_experiencia',
        'cpf_supervisor',
        'celular_supervisor',
        'email_supervisor'
    ];

    // Definir o relacionamento com a empresa
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'fk_id_empresa', 'id_empresa');
    }

    public function alteracoes()
    {
        return $this->hasMany(AlteracaoTermo::class, 'fk_id_supervisor');
    }

    public function termos()
    {
        return $this->hasMany(Termo::class, 'fk_id_supervisor');
    }


}
