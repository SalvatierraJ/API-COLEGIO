<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ComunaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'region_id' => $this->region_id,
            'nombre'    => $this->nombre,
        ];
    }
}
