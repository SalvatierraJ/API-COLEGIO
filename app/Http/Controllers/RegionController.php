<?php

namespace App\Http\Controllers;

use App\Http\Resources\RegionResource;
use App\Services\RegionService;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function __construct(private RegionService $service) {}

    /**
     * Listar regiones (opcionalmente con sus comunas)
     *
     * @OA\Get(
     *   path="/regiones",
     *   tags={"Regiones"},
     *   summary="Listar regiones",
     *   description="Devuelve el listado de regiones. Si se pasa ?include=comunas, incluye el arreglo de comunas por región.",
     *   @OA\Parameter(
     *     name="include",
     *     in="query",
     *     required=false,
     *     description="Incluir relaciones",
     *     @OA\Schema(type="string", enum={"comunas"}),
     *     example="comunas"
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Listado de regiones",
     *     @OA\JsonContent(ref="#/components/schemas/RegionsResponse")
     *   )
     * )
     */
    public function index(Request $request)
    {
        $include = $request->query('include');
        if ($include === 'comunas') {
            $data = $this->service->listAllWithComunas();
            return RegionResource::collection($data);
        }

        $data = $this->service->listAllWithComunas()->map(function ($r) {
            $r->setRelation('comunas', collect()); // vacío
            return $r;
        });
        return RegionResource::collection($data);
    }

    /**
     * Obtener comunas por región
     *
     * @OA\Get(
     *   path="/regiones/{regionId}/comunas",
     *   tags={"Regiones"},
     *   summary="Obtener comunas por ID de región",
     *   description="Devuelve la región solicitada con su arreglo de comunas.",
     *   @OA\Parameter(
     *     name="regionId",
     *     in="path",
     *     required=true,
     *     description="ID de la región",
     *     @OA\Schema(type="integer"),
     *     example=13
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Región con comunas",
     *     @OA\JsonContent(ref="#/components/schemas/RegionResponse")
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="No encontrado",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorMessage")
     *   )
     * )
     */
    public function comunas(int $regionId)
    {
        $region = $this->service->comunasByRegionId($regionId);
        if (! $region) {
            return response()->json(['message' => 'Región no encontrada'], 404);
        }
        return new RegionResource($region);
    }
}
