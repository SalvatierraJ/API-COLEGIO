<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstitucionWizardResource extends JsonResource
{
    public static $wrap = null;

    public function toArray($request): array
    {
        $inst = $this->resource;
        $colegios = $inst->colegios ?? collect();

        $usuarios = $colegios->flatMap(function ($col) {
            $rels = $col->personal_educativo ?? collect();
            return $rels->map(function ($pe) use ($col) {
                $p = $pe->persona;
                return [
                    'nombres'   => $p->nombres ?? '',
                    'apellidos' => $p->apellidos ?? '',
                    'rut'       => $p->rut ?? '',
                    'telefono'  => $p->telefono ?? null,
                    'correo'    => $p->correo ?? null,
                    'colegioId' => $col->id,
                ];
            });
        })->values();


        $encargadoPersona = property_exists($inst, 'encargado') ? $inst->encargado : null;

        if (!$encargadoPersona) {
            $encargadoPersona = $colegios
                ->flatMap(function ($c) {
                    $rels = $c->personal_educativo ?? collect();
                    return $rels->map(function ($pe) {
                        return $pe->persona;
                    })->filter();
                })
                ->first();
        }

        $encargado = $encargadoPersona ? [
            'id'        => $encargadoPersona->id,
            'nombres'   => $encargadoPersona->nombres,
            'apellidos' => $encargadoPersona->apellidos,
            'rut'       => $encargadoPersona->rut,
            'telefono'  => $encargadoPersona->telefono,
            'correo'    => $encargadoPersona->correo,
        ] : null;

        return [
            'id'      => $inst->id,
            'cliente' => [
                'nombre'       => $inst->nombre,
                'rut'          => $inst->rut,
                'telefono'     => $inst->telefono,
                'direccion'    => $inst->direccion,
                'fecha_inicio' => optional($inst->fecha_inicio)->format('Y-m-d'),
                'regionId'     => optional($inst->comuna)->region_id,
                'comunaId'     => $inst->comuna_id,
            ],
            'colegios' => $colegios->map(function ($c) {
                return [
                    'id'         => $c->id,
                    'nombre'     => $c->nombre,
                    'rut'        => $c->rut,
                    'telefono'   => $c->telefono,
                    'direccion'  => $c->direccion,
                    'regionId'   => optional($c->comuna)->region_id,
                    'comunaId'   => $c->comuna_id,
                ];
            })->values(),
            'usuarios'  => $usuarios,
            'estado'    => $inst->estado,
            'encargado' => $encargado,
        ];
    }
}
