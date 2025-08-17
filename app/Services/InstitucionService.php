<?php

namespace App\Services;

use App\Models\Colegio;
use App\Models\Institucione;
use App\Models\Persona;
use App\Models\PersonalEducativo;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstitucionService
{

    private static function normalizeRut(?string $rut): string
    {
        $rut = trim((string)$rut);
        $rut = str_replace(['.', '-', ' '], '', $rut);
        return strtolower($rut);
    }

    private function findInstitucionByRut(string $rut): ?Institucione
    {
        $norm = self::normalizeRut($rut);
        return Institucione::query()
            ->where('rut', $rut)
            ->orWhereRaw("REPLACE(REPLACE(LOWER(rut),'.',''),'-','') = ?", [$norm])
            ->lockForUpdate()
            ->first();
    }

    private function findPersonaByRut(?string $rut)
    {
        if (!$rut) return null;
        $norm = self::normalizeRut($rut);

        return Persona::query()
            ->where('rut', $rut)
            ->orWhereRaw("REPLACE(REPLACE(LOWER(rut),'.',''),'-','') = ?", [$norm])
            ->lockForUpdate()
            ->first();
    }

    private function mapPersonaForPayload(?Persona $p): ?array
    {
        if (!$p) return null;

        return [
            'id'        => $p->id,
            'nombres'   => $p->nombres,
            'apellidos' => $p->apellidos,
            'rut'       => $p->rut,
            'telefono'  => $p->telefono,
            'correo'    => $p->correo,
        ];
    }
    /**
     * Estructura esperada en $payload:
     * [
     *   'cliente' => ['nombre','rut','telefono','direccion','fecha_inicio','regionId','comunaId'],
     *   'colegios' => [ { id(client-key o id DB), nombre,rut,telefono,direccion,regionId,comunaId }, ... ],
     *   'usuarios' => [ { nombres,apellidos,rut,telefono,correo,colegioId(client-key o id DB) }, ... ]
     * ]
     */
    public function createFromWizard(array $payload): Institucione
    {

        return DB::transaction(function () use ($payload) {
            $personaId = optional(Auth::user()?->persona)->id
                ?? Auth::user()->persona_id
                ?? null;
            $cliente = $payload['cliente'] ?? [];
            $colegios = $payload['colegios'] ?? [];
            $usuarios = $payload['usuarios'] ?? [];

            $cliente = $payload['cliente'] ?? [];
            $rutRaw  = trim($cliente['rut'] ?? '');

            if ($rutRaw === '') {
                throw ValidationException::withMessages(['cliente.rut' => ['El RUT de la institución es obligatorio.']]);
            }


            if ($this->findInstitucionByRut($rutRaw)) {
                throw ValidationException::withMessages([
                    'cliente.rut' => ["El RUT {$rutRaw} ya está registrado en otra institución. Usa actualización o cambia el RUT."]
                ]);
            }

            /** @var Institucione $inst */
            $inst = Institucione::create([
                'comuna_id'    => $cliente['comunaId'] ?? null,
                'nombre'       => $cliente['nombre'] ?? '',
                'rut'          => $rutRaw,
                'telefono'     => $cliente['telefono'] ?? null,
                'direccion'    => $cliente['direccion'] ?? null,
                'fecha_inicio' => $cliente['fecha_inicio'] ?? null,
                'estado'       => 'En Proceso',
                'delete_status' => false,
                'registrado_por_persona_id' => $personaId,
            ]);


            $clientKeyToId = [];
            $seenRuts = [];

            foreach ($colegios as $c) {
                $rut = trim($c['rut'] ?? '');
                if ($rut === '') {
                    throw ValidationException::withMessages([
                        'colegios' => ['Falta el RUT de un colegio.'],
                    ]);
                }

                if (isset($seenRuts[$rut])) {
                    $colegio = $seenRuts[$rut];
                } else {
                    $existing = Colegio::where('rut', $rut)->lockForUpdate()->first();

                    if ($existing) {
                        if ((int) $existing->institucion_id !== (int) $inst->id) {
                            throw ValidationException::withMessages([
                                'colegios' => ["El RUT {$rut} ya está registrado en el colegio \"{$existing->nombre}\" (institución ID {$existing->institucion_id})."],
                            ]);
                        }

                        $existing->fill([
                            'comuna_id' => $c['comunaId'] ?? $existing->comuna_id,
                            'nombre'    => $c['nombre']    ?? $existing->nombre,
                            'direccion' => $c['direccion'] ?? $existing->direccion,
                            'telefono'  => $c['telefono']  ?? $existing->telefono,
                        ])->save();

                        $colegio = $existing;
                    } else {
                        $colegio = Colegio::create([
                            'institucion_id' => $inst->id,
                            'comuna_id'      => $c['comunaId'] ?? null,
                            'rut'            => $rut,
                            'nombre'         => $c['nombre'] ?? '',
                            'direccion'      => $c['direccion'] ?? null,
                            'telefono'       => $c['telefono'] ?? null,
                            'delete_status'  => false,
                        ]);
                    }

                    $seenRuts[$rut] = $colegio;
                }

                if (!empty($c['id']) && !is_numeric($c['id'])) {
                    $clientKeyToId[$c['id']] = $colegio->id;
                }
            }


            $seenPersonaByNorm = [];

            foreach ($usuarios as $u) {
                $rawColegioId = $u['colegioId'] ?? null;
                $colegioId = null;

                if (is_numeric($rawColegioId)) {
                    $colegioId = (int) $rawColegioId;
                } elseif (!empty($rawColegioId) && isset($clientKeyToId[$rawColegioId])) {
                    $colegioId = $clientKeyToId[$rawColegioId];
                }
                if (!$colegioId) {
                    continue;
                }

                $rutRaw = trim($u['rut'] ?? '');
                if ($rutRaw === '') {
                    throw ValidationException::withMessages([
                        'usuarios' => ['Falta el RUT de un usuario.'],
                    ]);
                }

                $norm = self::normalizeRut($rutRaw);
                $persona = $seenPersonaByNorm[$norm] ?? null;

                if (!$persona) {
                    $persona = $this->findPersonaByRut($rutRaw);

                    if ($persona) {
                        $persona->fill([
                            'nombres'   => $u['nombres']   ?? $persona->nombres,
                            'apellidos' => $u['apellidos'] ?? $persona->apellidos,
                            'telefono'  => $u['telefono']  ?? $persona->telefono,
                            'correo'    => $u['correo']    ?? $persona->correo,
                        ])->save();
                    } else {
                        try {
                            $persona = Persona::create([
                                'nombres'       => $u['nombres']   ?? '',
                                'apellidos'     => $u['apellidos'] ?? '',
                                'rut'           => $rutRaw,
                                'telefono'      => $u['telefono']  ?? null,
                                'correo'        => $u['correo']    ?? null,
                                'delete_status' => false,
                            ]);
                        } catch (QueryException $e) {
                            if (($e->errorInfo[1] ?? null) === 1062) {
                                $persona = $this->findPersonaByRut($rutRaw);
                                if (!$persona) {
                                    throw ValidationException::withMessages([
                                        'usuarios' => ["No se pudo crear la persona con RUT {$rutRaw}."],
                                    ]);
                                }
                            } else {
                                throw $e;
                            }
                        }
                    }

                    $seenPersonaByNorm[$norm] = $persona;
                }

                $pe = PersonalEducativo::firstOrNew(['persona_id' => $persona->id]);
                $pe->colegio_id    = $colegioId;
                $pe->delete_status = false;
                $pe->save();
            }

            return $this->loadForWizard($inst->id);
        });
    }

    public function updateFromWizard(int $institucionId, array $payload): Institucione
    {
        return DB::transaction(function () use ($institucionId, $payload) {

            $personaId = optional(Auth::user()?->persona)->id
                ?? Auth::user()->persona_id
                ?? null;
            /** @var Institucione $inst */
            $inst = Institucione::lockForUpdate()->findOrFail($institucionId);

            $cliente  = $payload['cliente']  ?? [];
            $colegios = $payload['colegios'] ?? [];
            $usuarios = $payload['usuarios'] ?? [];

            $newRut = trim($cliente['rut'] ?? $inst->rut);
            if ($newRut !== $inst->rut) {
                $dup = Institucione::query()
                    ->where(function (Builder $q) use ($newRut) {
                        $norm = self::normalizeRut($newRut);
                        $q->where('rut', $newRut)
                            ->orWhereRaw("REPLACE(REPLACE(LOWER(rut),'.',''),'-','') = ?", [$norm]);
                    })
                    ->where('id', '<>', $inst->id)
                    ->lockForUpdate()
                    ->first();

                if ($dup) {
                    throw ValidationException::withMessages([
                        'cliente.rut' => ["El RUT {$newRut} ya pertenece a la institución \"{$dup->nombre}\" (ID {$dup->id})."]
                    ]);
                }
                $inst->rut = $newRut;
            }

            $inst->fill([
                'comuna_id'    => $cliente['comunaId'] ?? $inst->comuna_id,
                'nombre'       => $cliente['nombre'] ?? $inst->nombre,
                'rut'          => $cliente['rut'] ?? $inst->rut,
                'telefono'     => $cliente['telefono'] ?? $inst->telefono,
                'direccion'    => $cliente['direccion'] ?? $inst->direccion,
                'fecha_inicio' => $cliente['fecha_inicio'] ?? $inst->fecha_inicio,
            ]);
            $inst->actualizado_por_persona_id = $personaId;

            $inst->save();


            $existing = $inst->colegios()->get()->keyBy('id');
            $seenIds  = [];
            $clientKeyToId = [];
            $seenRuts = [];

            foreach ($colegios as $c) {
                $rut = trim($c['rut'] ?? '');
                if ($rut === '') {
                    throw ValidationException::withMessages([
                        'colegios' => ['Falta el RUT de un colegio.'],
                    ]);
                }

                $isNumericId = !empty($c['id']) && is_numeric($c['id']);
                $colegio = null;

                if ($isNumericId && $existing->has((int)$c['id'])) {
                    $colegio = $existing[(int)$c['id']];

                    if ($rut !== $colegio->rut) {
                        $dup = Colegio::where('rut', $rut)->lockForUpdate()->first();
                        if ($dup && (int)$dup->id !== (int)$colegio->id) {
                            if ((int)$dup->institucion_id !== (int)$inst->id) {
                                throw ValidationException::withMessages([
                                    'colegios' => ["El RUT {$rut} ya está registrado en el colegio \"{$dup->nombre}\" (institución ID {$dup->institucion_id})."],
                                ]);
                            }
                            throw ValidationException::withMessages([
                                'colegios' => ["El RUT {$rut} ya existe en esta institución asociado al colegio \"{$dup->nombre}\" (ID {$dup->id})."],
                            ]);
                        }
                    }

                    $colegio->fill([
                        'comuna_id' => $c['comunaId'] ?? $colegio->comuna_id,
                        'rut'       => $rut ?: $colegio->rut,
                        'nombre'    => $c['nombre'] ?? $colegio->nombre,
                        'direccion' => $c['direccion'] ?? $colegio->direccion,
                        'telefono'  => $c['telefono'] ?? $colegio->telefono,
                    ])->save();

                    $seenIds[] = $colegio->id;
                } else {
                    if (isset($seenRuts[$rut])) {
                        $colegio = $seenRuts[$rut];
                    } else {
                        $existingByRut = Colegio::where('rut', $rut)->lockForUpdate()->first();
                        if ($existingByRut) {
                            if ((int)$existingByRut->institucion_id !== (int)$inst->id) {
                                throw ValidationException::withMessages([
                                    'colegios' => ["El RUT {$rut} ya está registrado en el colegio \"{$existingByRut->nombre}\" (institución ID {$existingByRut->institucion_id})."],
                                ]);
                            }
                            $existingByRut->fill([
                                'comuna_id' => $c['comunaId'] ?? $existingByRut->comuna_id,
                                'nombre'    => $c['nombre'] ?? $existingByRut->nombre,
                                'direccion' => $c['direccion'] ?? $existingByRut->direccion,
                                'telefono'  => $c['telefono'] ?? $existingByRut->telefono,
                            ])->save();

                            $colegio = $existingByRut;
                        } else {
                            $colegio = Colegio::create([
                                'institucion_id' => $inst->id,
                                'comuna_id'      => $c['comunaId'] ?? null,
                                'rut'            => $rut,
                                'nombre'         => $c['nombre'] ?? '',
                                'direccion'      => $c['direccion'] ?? null,
                                'telefono'       => $c['telefono'] ?? null,
                                'delete_status'  => false,
                            ]);
                        }
                        $seenRuts[$rut] = $colegio;
                    }

                    $seenIds[] = $colegio->id;

                    if (!empty($c['id']) && !is_numeric($c['id'])) {
                        $clientKeyToId[$c['id']] = $colegio->id;
                    }
                }
            }

            $toDelete = $existing->keys()->diff($seenIds);
            if ($toDelete->count()) {
                Colegio::whereIn('id', $toDelete)->delete();
            }


            $toDelete = $existing->keys()->diff($seenIds);
            if ($toDelete->count()) {
                Colegio::whereIn('id', $toDelete)->delete();
            }


            foreach ($usuarios as $u) {
                $rawColegioId = $u['colegioId'] ?? null;
                $colegioId = null;
                if (is_numeric($rawColegioId)) {
                    $colegioId = (int) $rawColegioId;
                } elseif (!empty($rawColegioId) && isset($clientKeyToId[$rawColegioId])) {
                    $colegioId = $clientKeyToId[$rawColegioId];
                }

                if (!$colegioId) {
                    continue;
                }

                $persona = Persona::where('rut', $u['rut'] ?? '')->first();
                if (!$persona) {
                    $persona = Persona::create([
                        'nombres'       => $u['nombres'] ?? '',
                        'apellidos'     => $u['apellidos'] ?? '',
                        'rut'           => $u['rut'] ?? '',
                        'telefono'      => $u['telefono'] ?? null,
                        'correo'        => $u['correo'] ?? null,
                        'delete_status' => false,
                    ]);
                } else {
                    $persona->fill([
                        'nombres'  => $u['nombres'] ?? $persona->nombres,
                        'apellidos' => $u['apellidos'] ?? $persona->apellidos,
                        'telefono' => $u['telefono'] ?? $persona->telefono,
                        'correo'   => $u['correo'] ?? $persona->correo,
                    ])->save();
                }

                $pivot = PersonalEducativo::firstOrNew([
                    'persona_id' => $persona->id,
                ]);
                $pivot->colegio_id    = $colegioId;
                $pivot->delete_status = false;
                $pivot->save();
            }

            return $this->loadForWizard($inst->id);
        });
    }

    public function getForWizard(int $id): Institucione
    {
        return $this->loadForWizard($id);
    }

    private function loadForWizard(int $id): Institucione
    {
        return Institucione::with([
            'comuna.region',
            'colegios.comuna',
            'colegios.personal_educativo.persona',
            'encargado',
        ])->findOrFail($id);
    }

    public function toWizardPayload(Institucione $inst): array
{
    $colegios = $inst->colegios()->with(['comuna'])->get();

    $usuarios = [];
    foreach ($colegios as $col) {
        $rels = PersonalEducativo::with('persona')
            ->where('colegio_id', $col->id)->get();

        foreach ($rels as $rel) {
            $p = $rel->persona;
            if (!$p) continue;

            $usuarios[] = [
                'nombres'   => $p->nombres,
                'apellidos' => $p->apellidos,
                'rut'       => $p->rut,
                'telefono'  => $p->telefono,
                'correo'    => $p->correo,
                'colegioId' => $col->id,
            ];
        }
    }


    $encargadoPersona = null;
    if (!empty($inst->encargado_persona_id)) {
        $encargadoPersona = Persona::find($inst->encargado_persona_id);
    }

    if (!$encargadoPersona) {
        $firstPE = PersonalEducativo::with('persona')
            ->whereIn('colegio_id', $colegios->pluck('id'))
            ->orderBy('id', 'asc')
            ->first();

        $encargadoPersona = $firstPE?->persona;
    }

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
        'colegios' => $colegios->map(function (Colegio $c) {
            return [
                'id'         => $c->id,
                'nombre'     => $c->nombre,
                'rut'        => $c->rut,
                'telefono'   => $c->telefono,
                'direccion'  => $c->direccion,
                'regionId'   => optional($c->comuna)->region_id,
                'comunaId'   => $c->comuna_id,
            ];
        })->values()->all(),
        'usuarios'   => $usuarios,
        'estado'     => $inst->estado,
        'encargado'  => $this->mapPersonaForPayload($encargadoPersona),
    ];
}


    public function paginate(array $params = [])
    {
        $q        = trim($params['q'] ?? '');
        $page     = (int)($params['page'] ?? 1);
        $perPage  = (int)($params['per_page'] ?? 15);
        $perPage  = $perPage > 0 ? min($perPage, 100) : 15;

        $sortBy   = $params['sort_by']  ?? 'created_at';
        $sortDir  = strtolower($params['sort_dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        $regionId     = $params['regionId']   ?? null;
        $comunaId     = $params['comunaId']   ?? null;
        $estado       = $params['estado']     ?? null;
        $fechaDesde   = $params['fecha_inicio_desde'] ?? null;
        $fechaHasta   = $params['fecha_inicio_hasta'] ?? null;
        $deleteStatus = array_key_exists('delete_status', $params) ? (bool)$params['delete_status'] : false;

        $table = (new Institucione())->getTable();

        $query = Institucione::query()
            ->with([
                'comuna:id,nombre,region_id',
                'comuna.region:id,nombre',
            ])
            ->withCount('colegios')
            ->addSelect([
                'personal_educativo_count' => PersonalEducativo::query()
                    ->selectRaw('COUNT(*)')
                    ->join('colegios', 'colegios.id', '=', 'personal_educativo.colegio_id')
                    ->whereColumn('colegios.institucion_id', $table . '.id')
            ])
            ->where($table . '.delete_status', $deleteStatus);

        if ($q !== '') {
            $query->where(function (Builder $qq) use ($q, $table) {
                $qq->where($table . '.nombre', 'like', '%' . $q . '%')
                    ->orWhere($table . '.rut', 'like', '%' . $q . '%');
            });
        }

        if (!empty($estado)) {
            $query->where($table . '.estado', $estado);
        }

        if (!empty($comunaId)) {
            $query->where($table . '.comuna_id', (int)$comunaId);
        }

        if (!empty($regionId)) {
            $query->whereHas('comuna', function (Builder $qr) use ($regionId) {
                $qr->where('region_id', (int)$regionId);
            });
        }

        if (!empty($fechaDesde)) {
            $query->whereDate($table . '.fecha_inicio', '>=', $fechaDesde);
        }
        if (!empty($fechaHasta)) {
            $query->whereDate($table . '.fecha_inicio', '<=', $fechaHasta);
        }

        $allowedSorts = [
            'id',
            'nombre',
            'rut',
            'created_at',
            'updated_at',
            'fecha_inicio',
            'colegios_count',
            'personal_educativo_count',
        ];
        if (!in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'created_at';
        }
        $query->orderBy($sortBy, $sortDir);

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function paginateForWizard(Request $request): LengthAwarePaginator
    {
        $params = [
            'q'        => $request->query('q', ''),
            'page'     => (int) $request->query('page', 1),
            'per_page' => (int) $request->query('per_page', 15),

            'sort_by'  => $request->query('sort_by', 'created_at'),
            'sort_dir' => $request->query('sort_dir', 'desc'),

            'regionId' => $request->query('regionId'),
            'comunaId' => $request->query('comunaId'),
            'estado'   => $request->query('estado'),

            'fecha_inicio_desde' => $request->query('fecha_inicio_desde'),
            'fecha_inicio_hasta' => $request->query('fecha_inicio_hasta'),


            'delete_status' => $request->has('delete_status')
                ? filter_var($request->query('delete_status'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)
                : false,
        ];

        return $this->paginate($params);
    }
}
