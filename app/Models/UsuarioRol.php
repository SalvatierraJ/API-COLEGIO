<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class UsuarioRol
 *
 * @property int $id
 * @property int $usuario_id
 * @property int $rol_id
 * @property bool $delete_status
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Role $role
 * @property Usuario $usuario
 *
 * @package App\Models
 */
class UsuarioRol extends Model
{
	use SoftDeletes;
	protected $table = 'usuario_rol';

	protected $casts = [
		'usuario_id' => 'int',
		'rol_id' => 'int',
		'delete_status' => 'bool'
	];

	protected $fillable = [
		'usuario_id',
		'rol_id',
		'delete_status'
	];

	public function role()
	{
		return $this->belongsTo(Role::class, 'rol_id');
	}

	public function usuario()
	{
		return $this->belongsTo(Usuario::class);
	}



}
