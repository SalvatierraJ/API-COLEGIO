<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInstitucionRequest;
use App\Http\Requests\UpdateInstitucionRequest;
use App\Http\Resources\InstitucionWizardResource;
use App\Services\InstitucionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class InstitucionController extends Controller
{
    public function __construct(private InstitucionService $svc) {}

    /**
     * @OA\Get(
     *   path="/instituciones",
     *   operationId="Instituciones_Index",
     *   tags={"Instituciones"},
     *   summary="Listar instituciones (Wizard)",
     *   description="Devuelve instituciones paginadas, con estructura del Wizard.",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="page", in="query", @OA\Schema(type="integer"), example=1),
     *   @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer"), example=15),
     *   @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
     *   @OA\Parameter(name="estado", in="query", @OA\Schema(type="string", enum={"En Proceso","Aceptado","Rechazado"})),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="data",
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/InstitucionWizard")
     *       ),
     *       @OA\Property(
     *         property="links",
     *         type="object",
     *         @OA\Property(property="first", type="string", nullable=true),
     *         @OA\Property(property="last",  type="string", nullable=true),
     *         @OA\Property(property="prev",  type="string", nullable=true),
     *         @OA\Property(property="next",  type="string", nullable=true)
     *       ),
     *       @OA\Property(
     *         property="meta",
     *         type="object",
     *         @OA\Property(property="current_page", type="integer"),
     *         @OA\Property(property="from", type="integer", nullable=true),
     *         @OA\Property(property="last_page", type="integer"),
     *         @OA\Property(property="path", type="string"),
     *         @OA\Property(property="per_page", type="integer"),
     *         @OA\Property(property="to", type="integer", nullable=true),
     *         @OA\Property(property="total", type="integer")
     *       )
     *     )
     *   )
     * )
     */
    public function index(Request $request)
    {
        $items = $this->svc->paginateForWizard($request);
        return InstitucionWizardResource::collection($items);
    }

    /**
     * @OA\Post(
     *   path="/instituciones",
     *   operationId="Instituciones_Store",
     *   tags={"Instituciones"},
     *   summary="Crear institución (Wizard)",
     *   security={{"bearerAuth":{}}},
     *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/InstitucionWizardCreateRequest")),
     *   @OA\Response(response=201, description="Creado",
     *     @OA\Header(header="Location", @OA\Schema(type="string", example="/api/instituciones/7")),
     *     @OA\JsonContent(ref="#/components/schemas/InstitucionWizardResponse")
     *   ),
     *   @OA\Response(response=422, description="Validación fallida",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorValidation")
     *   )
     * )
     */
    public function store(StoreInstitucionRequest $request)
    {
        try {
            $inst = $this->svc->createFromWizard($request->validated());
            $inst = $this->svc->getForWizard($inst->id);
            return (new InstitucionWizardResource($inst))
                ->response()
                ->header('Location', route('instituciones.show', ['id' => $inst->id]))
                ->setStatusCode(201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * @OA\Get(
     *   path="/instituciones/{id}",
     *   operationId="Instituciones_Show",
     *   tags={"Instituciones"},
     *   summary="Mostrar institución (Wizard)",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=7),
     *   @OA\Response(response=200, description="OK",
     *     @OA\JsonContent(ref="#/components/schemas/InstitucionWizardResponse")
     *   ),
     *   @OA\Response(response=404, description="No encontrado",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorMessage")
     *   )
     * )
     */
    public function show(int $id)
    {
        $inst = $this->svc->getForWizard($id);
        return new InstitucionWizardResource($inst);
    }

    /**
     * @OA\Put(
     *   path="/instituciones/{id}",
     *   operationId="Instituciones_Update",
     *   tags={"Instituciones"},
     *   summary="Actualizar institución (Wizard)",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=7),
     *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/InstitucionWizardUpdateRequest")),
     *   @OA\Response(response=200, description="OK",
     *     @OA\JsonContent(ref="#/components/schemas/InstitucionWizardResponse")
     *   ),
     *   @OA\Response(response=404, description="No encontrado",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorMessage")
     *   ),
     *   @OA\Response(response=422, description="Validación fallida",
     *     @OA\JsonContent(ref="#/components/schemas/ErrorValidation")
     *   )
     * )
     */
    public function update(UpdateInstitucionRequest $request, int $id)
    {
        $inst = $this->svc->updateFromWizard($id, $request->validated());
        $inst = $this->svc->getForWizard($inst->id);
        return new InstitucionWizardResource($inst);
    }
}
