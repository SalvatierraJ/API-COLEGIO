<?php

namespace App\Swagger;

/**
 * Esquemas comunes para respuestas de error
 *
 * @OA\Schema(
 *   schema="ErrorMessage",
 *   type="object",
 *   @OA\Property(property="message", type="string", example="No encontrado")
 * )
 *
 * @OA\Schema(
 *   schema="ErrorValidation",
 *   type="object",
 *   @OA\Property(
 *     property="errors",
 *     type="object",
 *     additionalProperties=@OA\Schema(
 *       type="array",
 *       @OA\Items(type="string", example="El campo nombre es obligatorio.")
 *     )
 *   )
 * )
 */
final class SchemasCommon {}
