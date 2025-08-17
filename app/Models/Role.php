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
 * Class Role
 * 
 * @property int $id
 * @property string $nombre
 * @property bool $delete_status
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|UsuarioRol[] $usuario_rols
 *
 * @package App\Models
 */
class Role extends Model
{
	use SoftDeletes;
	protected $table = 'roles';

	protected $casts = [
		'delete_status' => 'bool'
	];

	protected $fillable = [
		'nombre',
		'delete_status'
	];

	public function usuario_rols()
	{
		return $this->hasMany(UsuarioRol::class, 'rol_id');
	}
}
