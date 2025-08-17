<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PersonalEducativo
 *
 * @property int $id
 * @property int $persona_id
 * @property int $colegio_id
 * @property bool $delete_status
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Persona $persona
 * @property Colegio $colegio
 */
class PersonalEducativo extends Model
{
    use SoftDeletes;

    protected $table = 'personal_educativo';

    protected $casts = [
        'persona_id'     => 'int',
        'colegio_id'     => 'int',
        'delete_status'  => 'bool',
        'deleted_at'     => 'datetime',
    ];

    protected $fillable = [
        'persona_id',
        'colegio_id',
        'delete_status',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function colegio()
    {
        return $this->belongsTo(Colegio::class);
    }
}
