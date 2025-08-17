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
 * Class Comuna
 * 
 * @property int $id
 * @property int $region_id
 * @property string $nombre
 * @property bool $delete_status
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Region $region
 * @property Collection|Colegio[] $colegios
 * @property Collection|Institucione[] $instituciones
 *
 * @package App\Models
 */
class Comuna extends Model
{
	use SoftDeletes;
	protected $table = 'comunas';

	protected $casts = [
		'region_id' => 'int',
		'delete_status' => 'bool'
	];

	protected $fillable = [
		'region_id',
		'nombre',
		'delete_status'
	];

	public function region()
	{
		return $this->belongsTo(Region::class);
	}

	public function colegios()
	{
		return $this->hasMany(Colegio::class);
	}

	public function instituciones()
	{
		return $this->hasMany(Institucione::class);
	}
}
