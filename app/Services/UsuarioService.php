<?php

namespace App\Services;

use App\Http\Resources\UsuarioResource;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class UsuarioService
{

    public function getAuthenticatedUser(): ?Usuario
    {
        return Auth::guard('api')->user();
    }


    public function getUserById(int $id): ?Usuario
    {
        return Usuario::with(['persona', 'usuario_rols'])->find($id);
    }


    public function getAuthenticatedUserWithRelations(): ?UsuarioResource
    {
        $user = Auth::guard('api')->user();
        if (! $user) return null;

         return new UsuarioResource($user);
    }
}
