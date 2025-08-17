<?php

namespace Database\Seeders;

use App\Models\Colegio;
use App\Models\Comuna;
use App\Models\Institucione;
use App\Models\Persona;
use App\Models\PersonalEducativo;
use App\Models\Usuario;
use App\Models\UsuarioRol;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoInstitucionesSeeder extends Seeder
{
    public function run(): void
    {
        if (Comuna::count() === 0) {
            $this->call(RegionComunaSeeder::class);
        }

        $faker = fake('es_CL');

        DB::transaction(function () use ($faker) {
            $usedRuts = [];
            $makeRut = function () use (&$usedRuts) {
                do {
                    $num = random_int(1000000, 29999999);
                    $rut = $this->formatRut($num);
                } while (isset($usedRuts[$rut]));
                $usedRuts[$rut] = true;
                return $rut;
            };

            $anyComunaId = fn() => Comuna::inRandomOrder()->value('id');

            $rolEncargado = Role::firstOrCreate(
                ['nombre' => 'Encargado'],
                ['delete_status' => false]
            );

            for ($i = 1; $i <= 10; $i++) {
                $instComunaId = $anyComunaId();

                $inst = Institucione::create([
                    'comuna_id'     => $instComunaId,
                    'nombre'        => 'Institución ' . $i,
                    'rut'           => $makeRut(),
                    'telefono'      => $faker->optional()->numerify('+56 9 #### ####'),
                    'estado'        => 'En Proceso',
                    'delete_status' => false,
                ]);

                $numColegios = random_int(2, 3);
                for ($c = 1; $c <= $numColegios; $c++) {
                    $colComunaId = $anyComunaId();

                    $colegio = Colegio::create([
                        'institucion_id' => $inst->id,
                        'comuna_id'      => $colComunaId,
                        'rut'            => $makeRut(),
                        'nombre'         => "Colegio {$i}-{$c}",
                        'direccion'      => $faker->streetAddress(),
                        'telefono'       => $faker->optional()->numerify('+56 9 #### ####'),
                        'delete_status'  => false,
                    ]);

                    $numPersonas = random_int(2, 4);
                    for ($p = 1; $p <= $numPersonas; $p++) {
                        $nombres   = $faker->firstName() . ' ' . $faker->optional(0.4, '')->firstName();
                        $apellidos = $faker->lastName() . ' ' . $faker->optional(0.6, '')->lastName();
                        $rut       = $makeRut();

                        $persona = Persona::create([
                            'nombres'       => trim($nombres),
                            'apellidos'     => trim($apellidos),
                            'rut'           => $rut,
                            'telefono'      => $faker->optional()->numerify('+56 9 #### ####'),
                            'correo'        => $faker->unique()->safeEmail(),
                            'delete_status' => false,
                        ]);

                        PersonalEducativo::create([
                            'persona_id'    => $persona->id,
                            'colegio_id'    => $colegio->id,
                            'delete_status' => false,
                        ]);

                        if ($p === 1) {
                            $usuario = Usuario::create([
                                'persona_id'     => $persona->id,
                                'nombre_usuario' => strtolower(explode(' ', $persona->nombres)[0]) . $colegio->id,
                                'contrasena'     => '123456',
                                'delete_status'  => false,
                            ]);

                            UsuarioRol::create([
                                'usuario_id'    => $usuario->id,
                                'rol_id'        => $rolEncargado->id,
                                'delete_status' => false,
                            ]);
                        }
                    }
                }
            }
        });
    }

    private function formatRut(int $num): string
    {
        $s = number_format($num, 0, '', '.');
        $dv = substr((string)($num * 7 + 3), -1);
        return "{$s}-{$dv}";
    }
}
