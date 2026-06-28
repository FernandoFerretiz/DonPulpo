<?php

namespace Database\Seeders;

use App\Models\Dish;
use App\Models\DishCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MenuSeeder extends Seeder
{
    private array $menu = [
        [
            'categoria' => 'Entradas',
            'platillos' => [
                ['Ostiones en su concha (6 piezas)',  250, 'Ostiones frescos servidos en su concha, ideales para iniciar con un sabor natural del mar.'],
                ['Ostiones en su concha (12 piezas)', 400, 'Docena de ostiones frescos en su concha, perfectos para compartir.'],
                ['Orden 3 quesadillas de camarón',    120, 'Tres quesadillas rellenas de camarón, servidas calientes y con gran sabor.'],
                ['Orden 3 quesadillas de Marlín',     110, 'Tres quesadillas rellenas de marlín, una opción sabrosa y práctica para comenzar.'],
                ['Papas a la francesa',                85, 'Papas doradas y crujientes, ideales como entrada o acompañamiento.'],
                ['Aros de cebolla',                   105, 'Aros de cebolla empanizados y fritos, crujientes por fuera y suaves por dentro.'],
                ['Dedos de queso',                     70, 'Bastones de queso empanizados, dorados y cremosos al centro.'],
                ['Guacamole',                         115, 'Guacamole fresco preparado al momento, ideal para acompañar tus platillos.'],
                ['Bardal',                            260, 'Especialidad de la casa preparada para compartir.'],
                ['Palomitas de pollo',                 80, 'Trozos pequeños de pollo empanizado, crujientes y fáciles de disfrutar.'],
            ],
        ],
        [
            'categoria' => 'Tostadas',
            'platillos' => [
                ['Tostada de ceviche de pescado',  80, 'Tostada crujiente con ceviche fresco de pescado, preparada con sabor ligero y marisquero.'],
                ['Tostada de ceviche de camarón', 100, 'Tostada con ceviche de camarón fresco, ideal para quienes buscan algo fresco y lleno de sabor.'],
                ['Tostada mixta',                 120, 'Tostada preparada con una combinación de mariscos frescos.'],
                ['Tostada de camarón',             90, 'Tostada crujiente servida con camarón fresco y preparación de la casa.'],
                ['Tostada de pulpo',              105, 'Tostada con pulpo preparado al estilo de la casa, fresca y sabrosa.'],
                ['Tostada de Marlín',              80, 'Tostada con marlín preparado, ideal para disfrutar un sabor intenso y diferente.'],
            ],
        ],
        [
            'categoria' => 'Ceviches',
            'platillos' => [
                ['Ceviche de pescado medio litro',           130, 'Ceviche fresco de pescado en presentación de medio litro.'],
                ['Ceviche de pescado litro',                 250, 'Ceviche fresco de pescado en presentación de litro, ideal para compartir.'],
                ['Ceviche de camarón y pescado medio litro', 210, 'Mezcla fresca de camarón y pescado en presentación de medio litro.'],
                ['Ceviche de camarón y pescado litro',       300, 'Ceviche mixto de camarón y pescado en presentación de litro.'],
                ['Ceviche de camarón medio litro',           180, 'Ceviche fresco de camarón en presentación de medio litro.'],
                ['Ceviche de camarón litro',                 310, 'Ceviche de camarón en presentación de litro, perfecto para compartir.'],
            ],
        ],
        [
            'categoria' => 'Aguachiles',
            'platillos' => [
                ['Aguachile de camarón (verde, rojo o negro)', 250, 'Camarón fresco bañado en salsa de aguachile a elegir: verde, rojo o negro.'],
            ],
        ],
        [
            'categoria' => 'Empanizados',
            'platillos' => [
                ['Mariscada individual',        250, 'Selección de mariscos empanizados en porción individual.'],
                ['Mariscada para dos personas', 400, 'Mariscada empanizada ideal para compartir entre dos personas.'],
                ['Mariscada para 4 personas',   700, 'Mariscada familiar con variedad de mariscos empanizados para compartir.'],
                ['Filete',                      170, 'Filete de pescado empanizado, dorado y crujiente.'],
                ['Camarón',                     230, 'Camarones empanizados y fritos, servidos con el sabor clásico de la casa.'],
                ['Rollo de filete',             250, 'Rollo de filete empanizado, preparado con una textura crujiente y sabor suave.'],
                ['Mixto filete y camarones',    245, 'Combinación de filete y camarones empanizados en un solo platillo.'],
            ],
        ],
        [
            'categoria' => 'Cocteles',
            'platillos' => [
                ['Coctel de camarón chico',       100, 'Coctel chico de camarón, fresco y preparado con salsa especial.'],
                ['Coctel de camarón mediano',      180, 'Coctel mediano de camarón, una opción fresca y clásica.'],
                ['Coctel de camarón grande',       230, 'Coctel grande de camarón, ideal para quienes buscan una porción más completa.'],
                ['Coctel de pulpo chico',          115, 'Coctel chico de pulpo preparado con salsa fresca de la casa.'],
                ['Coctel de pulpo mediano',        195, 'Coctel mediano de pulpo, fresco y con gran sabor.'],
                ['Coctel de pulpo grande',         245, 'Coctel grande de pulpo, servido en una porción generosa.'],
                ['Campechano mixto mediano',       200, 'Coctel mediano con mezcla de mariscos, preparado al estilo campechano.'],
                ['Campechano mixto grande',        250, 'Coctel grande con variedad de mariscos en preparación campechana.'],
                ['Vuelve a la vida mediano',       205, 'Coctel mixto de mariscos con sabor intenso y refrescante.'],
                ['Vuelve a la vida grande',        255, 'Porción grande de vuelve a la vida, con mezcla de mariscos y preparación especial.'],
            ],
        ],
        [
            'categoria' => 'Caldos',
            'platillos' => [
                ['Sopa de mariscos 1/2',  160, 'Media porción de sopa caliente con variedad de mariscos.'],
                ['Sopa de mariscos',      250, 'Sopa de mariscos servida caliente, con sabor casero y abundante.'],
                ['Caldo de filete 1/2',   120, 'Media porción de caldo caliente preparado con filete de pescado.'],
                ['Caldo de filete',       160, 'Caldo de filete de pescado, reconfortante y lleno de sabor.'],
                ['Caldo de camarón 1/2',  140, 'Media porción de caldo de camarón preparado al momento.'],
                ['Caldo de camarón',      180, 'Caldo caliente de camarón con sabor tradicional.'],
                ['Caldo Mixto',           190, 'Caldo preparado con combinación de mariscos.'],
                ['Veneno de camarón',     220, 'Caldo intenso de camarón, preparado para quienes disfrutan sabores fuertes y marisqueros.'],
            ],
        ],
        [
            'categoria' => 'Fritos',
            'platillos' => [
                ['Mojarra frita', 250, 'Mojarra entera frita, dorada por fuera y suave por dentro.'],
                ['Huachinango',   450, 'Huachinango frito, preparado al momento con sabor fresco y tradicional.'],
            ],
        ],
        [
            'categoria' => 'Plancha',
            'platillos' => [
                ['Filete a la plancha',             180, 'Filete de pescado cocinado a la plancha, ligero y sabroso.'],
                ['Filete a la plancha gratinado',   210, 'Filete a la plancha cubierto con queso gratinado.'],
                ['Camarones a la plancha',          220, 'Camarones cocinados a la plancha, con sabor natural y preparación ligera.'],
                ['Camarones a la plancha gratinado',245, 'Camarones a la plancha con queso gratinado.'],
                ['Mixto a la plancha',              250, 'Combinación de filete y camarones preparados a la plancha.'],
                ['Mixto a la plancha gratinado',    265, 'Filete y camarones a la plancha con queso gratinado.'],
                ['Filete al vapor',                 260, 'Filete de pescado cocinado al vapor, suave y ligero.'],
            ],
        ],
        [
            'categoria' => 'Filetes',
            'platillos' => [
                ['Filete a la mantequilla',  250, 'Filete de pescado preparado con mantequilla, de sabor suave y cremoso.'],
                ['Filete gratinado',         250, 'Filete de pescado cubierto con queso gratinado.'],
                ['Filete a la mexicana',     250, 'Filete preparado con ingredientes estilo mexicano.'],
                ['Filete a la diabla',       250, 'Filete bañado en salsa diabla, ideal para quienes disfrutan el picante.'],
                ['Filete al ajillo',         250, 'Filete preparado con ajo y un toque de chile, de sabor intenso.'],
                ['Filete a la veracruzana',  250, 'Filete preparado al estilo veracruzano, con salsa tradicional.'],
                ['Filete al mojo de ajo',    250, 'Filete cocinado con ajo, mantequilla y sazón de la casa.'],
                ['Filete ranchero',          250, 'Filete preparado con salsa ranchera y sabor casero.'],
            ],
        ],
        [
            'categoria' => 'Camarones',
            'platillos' => [
                ['Camarones a la mantequilla',          265, 'Camarones preparados con mantequilla, suaves y llenos de sabor.'],
                ['Camarones al mojo de ajo',            265, 'Camarones cocinados con ajo y sazón tradicional.'],
                ['Camarones rancheros',                 265, 'Camarones preparados con salsa ranchera.'],
                ['Camarones a la mexicana',             265, 'Camarones preparados con ingredientes estilo mexicano.'],
                ['Camarones a la diabla',               265, 'Camarones bañados en salsa diabla, con un toque picante.'],
                ['Camarones al ajillo',                 265, 'Camarones preparados con ajo y chile, de sabor intenso.'],
                ['Camarones gratinados',                270, 'Camarones cubiertos con queso gratinado.'],
                ['Camarones a la veracruzana',          265, 'Camarones preparados al estilo veracruzano.'],
                ['Camarones encebollados a la diabla',  270, 'Camarones con cebolla y salsa diabla, una opción picante y sabrosa.'],
                ['Camarones Envueltos',                 285, 'Camarones envueltos y preparados al estilo de la casa.'],
                ['Camarones Macuil para pelar (8 piezas)', 250, 'Ocho camarones para pelar, servidos con preparación especial de la casa.'],
            ],
        ],
        [
            'categoria' => 'Combos',
            'platillos' => [
                ['Combo 1 - Filete, Camarones Envueltos, Posta de Mojarra y aros de cebolla, arroz, papas y ensalada', 265, 'Combo completo con filete, camarones envueltos, posta de mojarra, aros de cebolla, arroz, papas y ensalada.'],
                ['Combo 2 - Medio caldo de filete, filete empanizado',                                                  270, 'Combo con medio caldo de filete y filete empanizado.'],
                ['Combo 3 - Coctel de camarón mediano y tostada de ceviche',                                            250, 'Combo fresco con coctel mediano de camarón y tostada de ceviche.'],
                ['Combo 4 - Camarones gratinados y medio caldo de filete',                                              350, 'Combo con camarones gratinados y medio caldo de filete.'],
                ['Combo 5 - 2 Mojarras',                                                                                480, 'Combo con dos mojarras fritas, ideal para compartir.'],
            ],
        ],
        [
            'categoria' => 'Menú infantil (todos los platillos incluyen papas a la francesa y jugo)',
            'platillos' => [
                ['Nuggets de pollo',    90, 'Nuggets de pollo para niños, servidos con papas a la francesa y jugo.'],
                ['Dedos de queso',      70, 'Bastones de queso empanizados, servidos con papas a la francesa y jugo.'],
                ['Papas a la francesa', 50, 'Papas doradas y crujientes, servidas como opción infantil.'],
                ['Fajitas de pollo',   160, 'Fajitas de pollo para niños, servidas con papas a la francesa y jugo.'],
                ['Palomitas de pollo', 120, 'Palomitas de pollo empanizadas, servidas con papas a la francesa y jugo.'],
                ['Juguito',             25, 'Jugo infantil para acompañar los platillos de los pequeños.'],
            ],
        ],
        [
            'categoria' => 'Bebidas',
            'platillos' => [
                ['Coca cola',      45, 'Refresco Coca-Cola frío para acompañar tus alimentos.'],
                ['Agua mineral',   45, 'Agua mineral refrescante.'],
                ['Joya de sabor',  45, 'Refresco Joya de sabor.'],
                ['Agua natural',   40, 'Agua natural embotellada.'],
            ],
        ],
    ];

    public function run(): void
    {
        $order             = 1;
        $categoriesCreated = 0;
        $dishesCreated     = 0;

        foreach ($this->menu as $entry) {
            $name = $entry['categoria'];
            $slug = Str::slug($name);

            $category = DishCategory::updateOrCreate(
                ['slug' => $slug],
                [
                    'name'          => $name,
                    'display_order' => $order,
                    'status'        => 'active',
                ]
            );

            $categoriesCreated++;

            foreach ($entry['platillos'] as [$dishName, $price, $description]) {
                Dish::updateOrCreate(
                    [
                        'dish_category_id' => $category->id,
                        'name'             => $dishName,
                    ],
                    [
                        'price'       => $price,
                        'status'      => 'active',
                        'description' => $description,
                        'image_path'  => null,
                    ]
                );

                $dishesCreated++;
            }

            $order++;
        }

        $this->command->info("Categorías: {$categoriesCreated}  |  Platillos: {$dishesCreated}");
    }
}
