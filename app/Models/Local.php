<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Local extends Model
{
    use HasFactory;

    // Nome real da tabela no banco (singular):
    protected $table = 'tb_local';
    protected $primaryKey = 'id_local';
    public $timestamps = false; // a tabela não possui created_at/updated_at

    protected $fillable = [
        'descricao',
        'fk_id_empresa',
    ];

    // Relacionamentos
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'fk_id_empresa', 'id_empresa');
    }
}
