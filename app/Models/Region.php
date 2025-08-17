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
 * Class Region
 * 
 * @property int $id
 * @property string $nombre
 * @property bool $delete_status
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Comuna[] $comunas
 *
 * @package App\Models
 */
class Region extends Model
{
	use SoftDeletes;
	protected $table = 'regions';

	protected $casts = [
		'delete_status' => 'bool'
	];

	protected $fillable = [
		'nombre',
		'delete_status'
	];

	public function comunas()
	{
		return $this->hasMany(Comuna::class);
	}
}
