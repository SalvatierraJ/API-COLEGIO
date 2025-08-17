<?php
namespace App\Services;

use App\Models\Persona;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthService {
    public function register(array $data): array {
         $persona = Persona::create([
            'nombres'   => $data['nombres']   ?? '',
            'apellidos' => $data['apellidos'] ?? '',
            'rut'       => $data['rut']       ?? '',
            'telefono'  => $data['telefono']  ?? '',
            'correo'    => $data['correo']    ?? '',
        ]);
        $user = Usuario::create([
            'persona_id'     => $persona->id,
            'nombre_usuario' => $data['nombre_usuario'],
            'contrasena'     =>Hash::make( $data['password']),
            'delete_status'  => false,
        ]);
        $token = JWTAuth::fromUser($user);
        return ['user' => $user, 'token' => $token];
    }

    public function login(string $usuario, string $password): ?string {
        if (! $token = Auth::guard('api')->attempt([
            'nombre_usuario' => $usuario,
            'password' => $password,
        ])) {
            return null;
        }
        return $token;
    }

    public function logout(): void
    {
        Auth::guard('api')->logout();
    }
}
