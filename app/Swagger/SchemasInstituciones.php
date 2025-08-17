<?php

namespace App\Swagger;

/**
 * ====== SCHEMAS PARA INSTITUCIONES (WIZARD) ======
 *
 * @OA\Schema(
 *   schema="ComunaShort",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=101),
 *   @OA\Property(property="nombre", type="string", example="Santiago")
 * )
 *
 * @OA\Schema(
 *   schema="ColegioWizard",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=21),
 *   @OA\Property(property="institucion_id", type="integer", example=7),
 *   @OA\Property(property="comuna_id", type="integer", example=101),
 *   @OA\Property(property="comuna", ref="#/components/schemas/ComunaShort"),
 *   @OA\Property(property="nombre", type="string", example="Colegio 1-2"),
 *   @OA\Property(property="direccion", type="string", example="Av. Siempre Viva 742"),
 *   @OA\Property(property="telefono", type="string", nullable=true, example="+56 9 1234 5678")
 * )
 *
 * @OA\Schema(
 *   schema="InstitucionWizard",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=7),
 *   @OA\Property(property="comuna_id", type="integer", example=13),
 *   @OA\Property(property="comuna", ref="#/components/schemas/ComunaShort"),
 *   @OA\Property(property="nombre", type="string", example="Institución 3"),
 *   @OA\Property(property="rut", type="string", example="12.345.678-9"),
 *   @OA\Property(property="telefono", type="string", nullable=true, example="+56 9 9876 5432"),
 *   @OA\Property(property="estado", type="string", enum={"En Proceso","Aceptado","Rechazado"}, example="En Proceso"),
 *   @OA\Property(property="colegios", type="array", @OA\Items(ref="#/components/schemas/ColegioWizard"))
 * )
 *
 * @OA\Schema(
 *   schema="InstitucionWizardResponse",
 *   type="object",
 *   @OA\Property(property="data", ref="#/components/schemas/InstitucionWizard")
 * )
 *
 * @OA\Schema(
 *   schema="PaginationLinks",
 *   type="object",
 *   @OA\Property(property="first", type="string", nullable=true),
 *   @OA\Property(property="last",  type="string", nullable=true),
 *   @OA\Property(property="prev",  type="string", nullable=true),
 *   @OA\Property(property="next",  type="string", nullable=true)
 * )
 *
 * @OA\Schema(
 *   schema="PaginationMeta",
 *   type="object",
 *   @OA\Property(property="current_page", type="integer", example=1),
 *   @OA\Property(property="from", type="integer", nullable=true, example=1),
 *   @OA\Property(property="last_page", type="integer", example=5),
 *   @OA\Property(property="path", type="string", example="http://api.test/api/instituciones"),
 *   @OA\Property(property="per_page", type="integer", example=15),
 *   @OA\Property(property="to", type="integer", nullable=true, example=15),
 *   @OA\Property(property="total", type="integer", example=67)
 * )
 *
 * @OA\Schema(
 *   schema="InstitucionesWizardPaginatedResponse",
 *   type="object",
 *   @OA\Property(property="data",  type="array", @OA\Items(ref="#/components/schemas/InstitucionWizard")),
 *   @OA\Property(property="links", ref="#/components/schemas/PaginationLinks"),
 *   @OA\Property(property="meta",  ref="#/components/schemas/PaginationMeta")
 * )
 *
 * @OA\Schema(
 *   schema="InstitucionWizardCreateRequest",
 *   type="object",
 *   required={"nombre","rut","comuna_id"},
 *   @OA\Property(property="nombre", type="string", example="Institución Demo"),
 *   @OA\Property(property="rut", type="string", example="12.345.678-9"),
 *   @OA\Property(property="telefono", type="string", nullable=true, example="+56 9 1234 5678"),
 *   @OA\Property(property="comuna_id", type="integer", example=13),
 *   @OA\Property(
 *     property="colegios",
 *     type="array",
 *     @OA\Items(
 *       type="object",
 *       required={"nombre","comuna_id","direccion"},
 *       @OA\Property(property="nombre", type="string", example="Colegio Central"),
 *       @OA\Property(property="comuna_id", type="integer", example=101),
 *       @OA\Property(property="direccion", type="string", example="Calle Falsa 123"),
 *       @OA\Property(property="telefono", type="string", nullable=true, example="+56 2 2222 2222")
 *     )
 *   )
 * )
 *
 * @OA\Schema(
 *   schema="InstitucionWizardUpdateRequest",
 *   type="object",
 *   @OA\Property(property="nombre", type="string", example="Institución Demo Actualizada"),
 *   @OA\Property(property="rut", type="string", example="12.345.678-9"),
 *   @OA\Property(property="telefono", type="string", nullable=true, example="+56 9 1234 5678"),
 *   @OA\Property(property="comuna_id", type="integer", example=13),
 *   @OA\Property(
 *     property="colegios",
 *     type="array",
 *     @OA\Items(
 *       type="object",
 *       @OA\Property(property="id", type="integer", example=21, description="Si existe, se actualiza; si no, se crea"),
 *       @OA\Property(property="nombre", type="string", example="Colegio Central"),
 *       @OA\Property(property="comuna_id", type="integer", example=101),
 *       @OA\Property(property="direccion", type="string", example="Calle Falsa 123"),
 *       @OA\Property(property="telefono", type="string", nullable=true, example="+56 2 2222 2222")
 *     )
 *   )
 * )
 */
final class SchemasInstituciones {}
