<?php

namespace App\Swagger;
/**
 * @OA\Schema(
 *   schema="Role",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="nombre", type="string", example="Encargado")
 * )
 *
 * @OA\Schema(
 *   schema="Persona",
 *   @OA\Property(property="id", type="integer", example=10),
 *   @OA\Property(property="nombres", type="string", example="Javier"),
 *   @OA\Property(property="apellidos", type="string", example="Salvatierra"),
 *   @OA\Property(property="rut", type="string", example="12.345.678-9"),
 *   @OA\Property(property="telefono", type="string", nullable=true, example="+56 9 1234 5678"),
 *   @OA\Property(property="correo", type="string", format="email", nullable=true, example="javier@example.com")
 * )
 *
 * @OA\Schema(
 *   schema="UsuarioSimple",
 *   @OA\Property(property="id", type="integer", example=5),
 *   @OA\Property(property="persona_id", type="integer", example=10),
 *   @OA\Property(property="nombre_usuario", type="string", example="javier01")
 * )
 *
 * @OA\Schema(
 *   schema="UsuarioPerfil",
 *   @OA\Property(property="id", type="integer", example=5),
 *   @OA\Property(property="nombre_usuario", type="string", example="javier01"),
 *   @OA\Property(property="persona_id", type="integer", example=10),
 *   @OA\Property(property="persona", ref="#/components/schemas/Persona"),
 *   @OA\Property(
 *     property="roles",
 *     type="array",
 *     @OA\Items(ref="#/components/schemas/Role")
 *   )
 * )
 *
 * @OA\Schema(
 *   schema="TokenResponse",
 *   @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGci..."),
 *   @OA\Property(property="token_type", type="string", example="bearer"),
 *   @OA\Property(property="expires_in", type="integer", example=3600)
 * )
 *
 * @OA\Schema(
 *   schema="MessageResponse",
 *   @OA\Property(property="message", type="string", example="Sesión cerrada correctamente")
 * )
 */
class SchemasAuth {}
