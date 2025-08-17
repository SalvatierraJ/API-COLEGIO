<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionComunaSeeder extends Seeder
{
    public function run(): void
    {
        $regionsTable = 'regions';
        $comunasTable = 'comunas';
        $now = now();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table($comunasTable)->truncate();
        DB::table($regionsTable)->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $dataset = [
            1 => ['nombre' => 'Arica y Parinacota', 'comunas' => ['Arica','Camarones','Putre','General Lagos']],
            2 => ['nombre' => 'Tarapacá', 'comunas' => ['Iquique','Alto Hospicio','Pozo Almonte','Camiña','Colchane','Huara','Pica']],
            3 => ['nombre' => 'Antofagasta', 'comunas' => ['Antofagasta','Mejillones','Sierra Gorda','Taltal','Calama','Ollagüe','San Pedro de Atacama','Tocopilla','María Elena']],
            4 => ['nombre' => 'Atacama', 'comunas' => ['Copiapó','Caldera','Tierra Amarilla','Chañaral','Diego de Almagro','Vallenar','Freirina','Huasco','Alto del Carmen']],
            5 => ['nombre' => 'Coquimbo', 'comunas' => ['La Serena','Coquimbo','Andacollo','La Higuera','Paihuano','Vicuña','Ovalle','Combarbalá','Monte Patria','Punitaqui','Río Hurtado','Illapel','Canela','Los Vilos','Salamanca']],
            6 => ['nombre' => 'Valparaíso', 'comunas' => [
                'Valparaíso','Viña del Mar','Concón','Quilpué','Villa Alemana','Limache','Olmué','Quintero','Puchuncaví',
                'Casablanca','Juan Fernández','Quillota','La Calera','La Cruz','Nogales','Hijuelas',
                'San Antonio','Cartagena','El Tabo','El Quisco','Algarrobo','Santo Domingo',
                'Petorca','La Ligua','Cabildo','Zapallar','Papudo',
                'Los Andes','San Esteban','Calle Larga','Rinconada',
                'San Felipe','Putaendo','Santa María','Panquehue','Llaillay','Catemu'
            ]],
            7 => ['nombre' => 'Metropolitana de Santiago', 'comunas' => [
                'Santiago','Cerrillos','Cerro Navia','Conchalí','El Bosque','Estación Central','Huechuraba','Independencia',
                'La Cisterna','La Florida','La Granja','La Pintana','La Reina','Las Condes','Lo Barnechea','Lo Espejo',
                'Lo Prado','Macul','Maipú','Ñuñoa','Pedro Aguirre Cerda','Peñalolén','Providencia','Pudahuel','Quilicura',
                'Quinta Normal','Recoleta','Renca','San Joaquín','San Miguel','San Ramón','Vitacura',
                'Puente Alto','Pirque','San José de Maipo','Colina','Lampa','Tiltil',
                'San Bernardo','Buin','Paine','Calera de Tango',
                'Melipilla','Alhué','Curacaví','María Pinto','San Pedro',
                'Talagante','El Monte','Isla de Maipo','Padre Hurtado','Peñaflor'
            ]],
            8 => ['nombre' => 'Libertador General Bernardo O\'Higgins', 'comunas' => [
                'Rancagua','Machalí','Graneros','Mostazal','Codegua','Coinco','Coltauco','Doñihue','Olivar','Peumo','Pichidegua','Quinta de Tilcoco','Rengo','Malloa','Requínoa','San Vicente',
                'Pichilemu','La Estrella','Litueche','Marchihue','Navidad','Paredones',
                'San Fernando','Chimbarongo','Nancagua','Placilla','Santa Cruz','Lolol','Pumanque','Peralillo','Palmilla'
            ]],
            9 => ['nombre' => 'Maule', 'comunas' => [
                'Talca','San Clemente','Pelarco','Pencahue','Maule','San Rafael','Curepto','Constitución','Empedrado','Río Claro',
                'Curicó','Teno','Romeral','Molina','Sagrada Familia','Hualañé','Licantén','Vichuquén','Rauco',
                'Linares','San Javier','Villa Alegre','Yerbas Buenas','Colbún','Longaví','Retiro','Parral',
                'Cauquenes','Chanco','Pelluhue'
            ]],
            10 => ['nombre' => 'Ñuble', 'comunas' => [
                'Chillán','Chillán Viejo','Pinto','Coihueco','El Carmen','San Ignacio','Pemuco','Yungay','Bulnes','Quillón',
                'San Nicolás','San Carlos','Ñiquén','Coelemu','Ránquil','Treguaco','Cobquecura','Portezuelo','Ninhue','Quirihue'
            ]],
            11 => ['nombre' => 'Biobío', 'comunas' => [
                'Concepción','Talcahuano','Hualpén','San Pedro de la Paz','Chiguayante','Penco','Tomé','Coronel','Lota',
                'Los Ángeles','Cabrero','Yumbel','Tucapel','Antuco','Quilleco','Santa Bárbara','Quilaco','Nacimiento','Negrete','Mulchén',
                'Curanilahue','Arauco','Lebu','Los Álamos','Cañete','Contulmo','Tirúa','Laja','San Rosendo','Hualqui','Florida'
            ]],
            12 => ['nombre' => 'La Araucanía', 'comunas' => [
                'Temuco','Padre Las Casas','Cunco','Melipeuco','Vilcún','Curarrehue','Pucón','Villarrica','Freire','Gorbea','Lautaro','Perquenco','Galvarino','Cholchol','Nueva Imperial','Carahue','Saavedra','Toltén','Teodoro Schmidt',
                'Angol','Renaico','Collipulli','Ercilla','Los Sauces','Purén','Lumaco','Traiguén','Victoria','Curacautín','Lonquimay'
            ]],
            13 => ['nombre' => 'Los Ríos', 'comunas' => [
                'Valdivia','Corral','Lanco','Los Lagos','Máfil','Mariquina','Paillaco','Panguipulli',
                'La Unión','Futrono','Lago Ranco','Río Bueno'
            ]],
            14 => ['nombre' => 'Los Lagos', 'comunas' => [
                'Puerto Montt','Puerto Varas','Frutillar','Llanquihue','Fresia','Los Muermos','Maullín','Cochamó',
                'Osorno','Río Negro','Purranque','Puyehue','San Pablo','San Juan de la Costa',
                'Castro','Ancud','Quellón','Dalcahue','Curaco de Vélez','Puqueldón','Queilén','Quinchao','Chonchi',
                'Chaitén','Futaleufú','Hualaihué','Palena'
            ]],
            15 => ['nombre' => 'Aysén del General Carlos Ibáñez del Campo', 'comunas' => [
                'Coyhaique','Lago Verde','Aysén','Cisnes','Guaitecas','Cochrane','O\'Higgins','Tortel','Chile Chico','Río Ibáñez'
            ]],
            16 => ['nombre' => 'Magallanes y de la Antártica Chilena', 'comunas' => [
                'Punta Arenas','Laguna Blanca','Río Verde','San Gregorio','Natales','Torres del Paine','Porvenir','Primavera','Timaukel','Cabo de Hornos'
            ]],
        ];

        $regionRows = [];
        foreach ($dataset as $id => $reg) {
            $regionRows[] = [
                'id'            => $id,
                'nombre'        => $reg['nombre'],
                'delete_status' => 0,
                'deleted_at'    => null,
                'created_at'    => $now,
                'updated_at'    => $now,
            ];
        }
        DB::table($regionsTable)->insert($regionRows);


        foreach ($dataset as $id => $reg) {
            $comunaRows = [];
            foreach ($reg['comunas'] as $c) {
                $comunaRows[] = [
                    'region_id'     => $id,
                    'nombre'        => $c,
                    'delete_status' => 0,
                    'deleted_at'    => null,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ];
            }

            foreach (array_chunk($comunaRows, 100) as $chunk) {
                DB::table($comunasTable)->insert($chunk);
            }
        }
    }
}
