<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use App\Services\UsuarioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function __construct(private AuthService $auth, private UsuarioService $usuarioService) {}

    /**
     * @OA\Post(
     *   path="/auth/register",
     *   tags={"Auth"},
     *   summary="Registrar un nuevo usuario",
     *   description="Crea un usuario con nombre de usuario y contraseña.",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"nombre_usuario","password"},
     *       @OA\Property(property="nombre_usuario", type="string", example="javier01"),
     *       @OA\Property(property="password", type="string", minLength=6, example="12345678")
     *     )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Usuario creado",
     *     @OA\JsonContent(ref="#/components/schemas/UsuarioSimple")
     *   ),
     *   @OA\Response(response=422, description="Validación fallida")
     * )
     */
    public function register(Request $r)
    {
        $data = $r->validate([
            'nombre_usuario' => 'required|string|unique:usuarios,nombre_usuario',
            'password'       => 'required|string|min:6',
        ]);
        $res = $this->auth->register($data);
        return response()->json($res, 201);
    }

    /**
     * @OA\Post(
     *   path="/auth/login",
     *   tags={"Auth"},
     *   summary="Iniciar sesión",
     *   description="Devuelve un JWT si las credenciales son válidas.",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"nombre_usuario","password"},
     *       @OA\Property(property="nombre_usuario", type="string", example="javier01"),
     *       @OA\Property(property="password", type="string", example="12345678")
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(ref="#/components/schemas/TokenResponse")
     *   ),
     *   @OA\Response(response=401, description="Credenciales inválidas")
     * )
     */
    public function login(LoginRequest $r)
    {
        $token = $this->auth->login($r->nombre_usuario, $r->password);
        if (! $token) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth('api')->factory()->getTTL() * 60
        ]);
    }

    /**
     * @OA\Get(
     *   path="/auth/me",
     *   tags={"Auth"},
     *   summary="Obtener perfil del usuario autenticado",
     *   security={{"bearerAuth":{}}},
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(ref="#/components/schemas/UsuarioPerfil")
     *   ),
     *   @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function obtenerPerfil()
    {
        $usuario = $this->usuarioService->getAuthenticatedUserWithRelations();

        if (! $usuario) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        return response()->json($usuario);
    }

    /**
     * @OA\Post(
     *   path="/auth/logout",
     *   tags={"Auth"},
     *   summary="Cerrar sesión",
     *   security={{"bearerAuth":{}}},
     *   @OA\Response(
     *     response=200,
     *     description="Sesión cerrada",
     *     @OA\JsonContent(ref="#/components/schemas/MessageResponse")
     *   ),
     *   @OA\Response(response=401, description="No autenticado")
     * )
     */
    public function logout(): JsonResponse
    {
        $this->auth->logout();
        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }
}
