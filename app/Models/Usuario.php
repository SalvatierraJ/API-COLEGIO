<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class Usuario extends Authenticatable implements JWTSubject
{
    use SoftDeletes;

    protected $table = 'usuarios';

    protected $casts = [
        'persona_id' => 'int',
        'delete_status' => 'bool',
        'deleted_at' => 'datetime',
    ];

    protected $fillable = [
        'persona_id',
        'nombre_usuario',
        'contrasena',
        'delete_status',
    ];

    protected $hidden = [
        'contrasena',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    public function setContrasenaAttribute($value)
    {
        $this->attributes['contrasena'] = \Illuminate\Support\Str::startsWith($value, '$2y$')
            ? $value
            : bcrypt($value);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'usuario' => $this->nombre_usuario,
            'persona_id' => $this->persona_id,
        ];
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function usuario_rols()
    {
        return $this->hasMany(UsuarioRol::class);
    }

    public function roles()
{
    return $this->belongsToMany(Role::class, 'usuario_rol', 'usuario_id', 'rol_id')
        ->withTimestamps()
        ->withPivot(['delete_status', 'deleted_at'])
        ->wherePivot('delete_status', false)
        ->whereNull('usuario_rol.deleted_at');
}
}
