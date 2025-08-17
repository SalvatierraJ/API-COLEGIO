<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Institucione
 *
 * @property int $id
 * @property int $comuna_id
 * @property string $nombre
 * @property string $rut
 * @property string|null $telefono
 * @property string|null $estado
 * @property bool $delete_status
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Comuna $comuna
 * @property Collection|Colegio[] $colegios
 *
 * @package App\Models
 */
class Institucione extends Model
{
    use SoftDeletes;
    protected $table = 'instituciones';

    protected $casts = [
        'comuna_id' => 'int',
        'delete_status' => 'bool'
    ];

    protected $fillable = [
        'comuna_id',
        'nombre',
        'rut',
        'telefono',
        'direccion',
        'fecha_inicio',
        'estado',
        'delete_status',
        'registrado_por_persona_id',
        'actualizado_por_persona_id',
    ];

    public function comuna()
    {
        return $this->belongsTo(Comuna::class);
    }

    public function colegios()
    {
        return $this->hasMany(Colegio::class, 'institucion_id');
    }

    public function registradoPorPersona()
    {
        return $this->belongsTo(Persona::class, 'registrado_por_persona_id');
    }

    public function actualizadoPorPersona()
    {
        return $this->belongsTo(Persona::class, 'actualizado_por_persona_id');
    }
    public function encargado()
    {
        return $this->belongsTo(Persona::class, 'encargado_persona_id');
    }
}
