<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Colegio
 *
 * @property int $id
 * @property int $institucion_id
 * @property int $comuna_id
 * @property string $rut
 * @property string $nombre
 * @property string|null $direccion
 * @property string|null $telefono
 * @property bool $delete_status
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Comuna $comuna
 * @property Institucione $institucione
 * @property Collection|PersonalEducativo[] $personal_educativo
 */
class Colegio extends Model
{
    use SoftDeletes;

    protected $table = 'colegios';

    protected $casts = [
        'institucion_id' => 'int',
        'comuna_id'      => 'int',
        'delete_status'  => 'bool',
        'deleted_at'     => 'datetime',
    ];

    protected $fillable = [
        'institucion_id',
        'comuna_id',
        'rut',
        'nombre',
        'direccion',
        'telefono',
        'delete_status',
    ];

    public function comuna()
    {
        return $this->belongsTo(Comuna::class);
    }

    public function institucione()
    {
        return $this->belongsTo(Institucione::class, 'institucion_id');
    }

    public function personal_educativo()
    {
        return $this->hasMany(PersonalEducativo::class);
    }

}
