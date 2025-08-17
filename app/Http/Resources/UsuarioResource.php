<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsuarioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'nombre_usuario' => $this->nombre_usuario,

            'persona' => [
                'id'        => $this->persona->id ?? null,
                'nombres'   => $this->persona->nombres ?? null,
                'apellidos' => $this->persona->apellidos ?? null,
                'rut'       => $this->persona->rut ?? null,
                'telefono'  => $this->persona->telefono ?? null,
                'correo'    => $this->persona->correo ?? null,
            ],

            'roles' => $this->roles->map(fn ($rol) => [
                'id'     => $rol->id,
                'nombre' => $rol->nombre,
            ]),
        ];
    }
}
