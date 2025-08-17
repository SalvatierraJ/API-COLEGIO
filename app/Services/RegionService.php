<?php
namespace App\Services;

use App\Models\Region;
use Illuminate\Support\Facades\Cache;

class RegionService
{
    public function listAllWithComunas()
    {
        return Cache::remember('regiones_with_comunas', 3600, function () {
            return Region::query()
                ->where('delete_status', false)
                ->select('id', 'nombre')
                ->with(['comunas' => function ($q) {
                    $q->where('delete_status', false)
                      ->select('id', 'region_id', 'nombre');
                }])
                ->orderBy('nombre')
                ->get();
        });
    }

    public function comunasByRegionId(int $regionId)
    {
        return Cache::remember("region_{$regionId}_comunas", 3600, function () use ($regionId) {
            return Region::query()
                ->where('id', $regionId)
                ->where('delete_status', false)
                ->with(['comunas' => function ($q) {
                    $q->where('delete_status', false)
                      ->select('id', 'region_id', 'nombre')
                      ->orderBy('nombre');
                }])
                ->first();
        });
    }
}
