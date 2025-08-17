<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Persona
 * 
 * @property int $id
 * @property string $nombres
 * @property string $apellidos
 * @property string $rut
 * @property string|null $telefono
 * @property string|null $correo
 * @property bool $delete_status
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property PersonalEducativo|null $personal_educativo
 * @property Usuario|null $usuario
 *
 * @package App\Models
 */
class Persona extends Model
{
	use SoftDeletes;
	protected $table = 'personas';

	protected $casts = [
		'delete_status' => 'bool'
	];

	protected $fillable = [
		'nombres',
		'apellidos',
		'rut',
		'telefono',
		'correo',
		'delete_status'
	];

	public function personal_educativo()
	{
		return $this->hasOne(PersonalEducativo::class);
	}

	public function usuario()
	{
		return $this->hasOne(Usuario::class);
	}
}
