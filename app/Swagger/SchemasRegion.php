<?php

namespace App\Swagger;

/**
 * @OA\Schema(
 *   schema="Comuna",
 *   @OA\Property(property="id", type="integer", example=101),
 *   @OA\Property(property="nombre", type="string", example="Santiago"),
 *   @OA\Property(property="region_id", type="integer", example=13, nullable=true)
 * )
 *
 * @OA\Schema(
 *   schema="Region",
 *   @OA\Property(property="id", type="integer", example=13),
 *   @OA\Property(property="nombre", type="string", example="Región Metropolitana de Santiago"),
 *   @OA\Property(
 *     property="comunas",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/Comuna"),
 *     description="Puede venir vacío si no se incluye ?include=comunas"
 *   )
 * )
 *
 * @OA\Schema(
 *   schema="RegionResponse",
 *   @OA\Property(property="data", ref="#/components/schemas/Region")
 * )
 *
 * @OA\Schema(
 *   schema="RegionsResponse",
 *   @OA\Property(
 *     property="data",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/Region")
 *   )
 * )
 */
class SchemasRegion {}
