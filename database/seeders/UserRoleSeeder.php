<?php

namespace Database\Seeders;

use App\Models\Persona;
use App\Models\Role;
use App\Models\Usuario;
use App\Models\UsuarioRol;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
   public function run(): void
    {
        $rol = Role::firstOrCreate(
            ['nombre' => 'Encargado'],
            ['delete_status' => false]
        );

        $personas = [
            [
                'nombres'   => 'Javier',
                'apellidos' => 'Salvatierra',
                'rut'       => '11111111-1',
                'telefono'  => '70000001',
                'correo'    => 'javier@example.com',
            ],
            [
                'nombres'   => 'Maria',
                'apellidos' => 'Pérez',
                'rut'       => '22222222-2',
                'telefono'  => '70000002',
                'correo'    => 'maria@example.com',
            ],
        ];

        foreach ($personas as $p) {
            $persona = Persona::firstOrCreate(
                ['rut' => $p['rut']],
                [
                    'nombres'       => $p['nombres'],
                    'apellidos'     => $p['apellidos'],
                    'telefono'      => $p['telefono'],
                    'correo'        => $p['correo'],
                    'delete_status' => false,
                ]
            );

            $usuario = Usuario::firstOrCreate(
                ['persona_id' => $persona->id],
                [
                    'nombre_usuario' => strtolower($p['nombres']),
                    'contrasena'     => '12345678',
                    'delete_status'  => false,
                ]
            );

            UsuarioRol::firstOrCreate(
                [
                    'usuario_id' => $usuario->id,
                    'rol_id'     => $rol->id,
                ],
                [
                    'delete_status' => false,
                ]
            );
        }
    }
}
