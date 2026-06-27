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
                ['Ostiones en su concha (6 piezas)', 250],
                ['Ostiones en su concha (12 piezas)', 400],
                ['Orden 3 quesadillas de camarón', 120],
                ['Orden 3 quesadillas de Marlín', 110],
                ['Papas a la francesa', 85],
                ['Aros de cebolla', 105],
                ['Dedos de queso', 70],
                ['Guacamole', 115],
                ['Bardal', 260],
                ['Palomitas de pollo', 80],
            ],
        ],
        [
            'categoria' => 'Tostadas',
            'platillos' => [
                ['Tostada de ceviche de pescado', 80],
                ['Tostada de ceviche de camarón', 100],
                ['Tostada mixta', 120],
                ['Tostada de camarón', 90],
                ['Tostada de pulpo', 105],
                ['Tostada de Marlín', 80],
            ],
        ],
        [
            'categoria' => 'Ceviches',
            'platillos' => [
                ['Ceviche de pescado medio litro', 130],
                ['Ceviche de pescado litro', 250],
                ['Ceviche de camarón y pescado medio litro', 210],
                ['Ceviche de camarón y pescado litro', 300],
                ['Ceviche de camarón medio litro', 180],
                ['Ceviche de camarón litro', 310],
            ],
        ],
        [
            'categoria' => 'Aguachiles',
            'platillos' => [
                ['Aguachile de camarón (verde, rojo o negro)', 250],
            ],
        ],
        [
            'categoria' => 'Empanizados',
            'platillos' => [
                ['Mariscada individual', 250],
                ['Mariscada para dos personas', 400],
                ['Mariscada para 4 personas', 700],
                ['Filete', 170],
                ['Camarón', 230],
                ['Rollo de filete', 250],
                ['Mixto filete y camarones', 245],
            ],
        ],
        [
            'categoria' => 'Cocteles',
            'platillos' => [
                ['Coctel de camarón chico', 100],
                ['Coctel de camarón mediano', 180],
                ['Coctel de camarón grande', 230],
                ['Coctel de pulpo chico', 115],
                ['Coctel de pulpo mediano', 195],
                ['Coctel de pulpo grande', 245],
                ['Campechano mixto mediano', 200],
                ['Campechano mixto grande', 250],
                ['Vuelve a la vida mediano', 205],
                ['Vuelve a la vida grande', 255],
            ],
        ],
        [
            'categoria' => 'Caldos',
            'platillos' => [
                ['Sopa de mariscos 1/2', 160],
                ['Sopa de mariscos', 250],
                ['Caldo de filete 1/2', 120],
                ['Caldo de filete', 160],
                ['Caldo de camarón 1/2', 140],
                ['Caldo de camarón', 180],
                ['Caldo Mixto', 190],
                ['Veneno de camarón', 220],
            ],
        ],
        [
            'categoria' => 'Fritos',
            'platillos' => [
                ['Mojarra frita', 250],
                ['Huachinango', 450],
            ],
        ],
        [
            'categoria' => 'Plancha',
            'platillos' => [
                ['Filete a la plancha', 180],
                ['Filete a la plancha gratinado', 210],
                ['Camarones a la plancha', 220],
                ['Camarones a la plancha gratinado', 245],
                ['Mixto a la plancha', 250],
                ['Mixto a la plancha gratinado', 265],
                ['Filete al vapor', 260],
            ],
        ],
        [
            'categoria' => 'Filetes',
            'platillos' => [
                ['Filete a la mantequilla', 250],
                ['Filete gratinado', 250],
                ['Filete a la mexicana', 250],
                ['Filete a la diabla', 250],
                ['Filete al ajillo', 250],
                ['Filete a la veracruzana', 250],
                ['Filete al mojo de ajo', 250],
                ['Filete ranchero', 250],
            ],
        ],
        [
            'categoria' => 'Camarones',
            'platillos' => [
                ['Camarones a la mantequilla', 265],
                ['Camarones al mojo de ajo', 265],
                ['Camarones rancheros', 265],
                ['Camarones a la mexicana', 265],
                ['Camarones a la diabla', 265],
                ['Camarones al ajillo', 265],
                ['Camarones gratinados', 270],
                ['Camarones a la veracruzana', 265],
                ['Camarones encebollados a la diabla', 270],
                ['Camarones Envueltos', 285],
                ['Camarones Macuil para pelar (8 piezas)', 250],
            ],
        ],
        [
            'categoria' => 'Combos',
            'platillos' => [
                ['Combo 1 - Filete, Camarones Envueltos, Posta de Mojarra y aros de cebolla, arroz, papas y ensalada', 265],
                ['Combo 2 - Medio caldo de filete, filete empanizado', 270],
                ['Combo 3 - Coctel de camarón mediano y tostada de ceviche', 250],
                ['Combo 4 - Camarones gratinados y medio caldo de filete', 350],
                ['Combo 5 - 2 Mojarras', 480],
            ],
        ],
        [
            'categoria' => 'Menú infantil (todos los platillos incluyen papas a la francesa y jugo)',
            'platillos' => [
                ['Nuggets de pollo', 90],
                ['Dedos de queso', 70],
                ['Papas a la francesa', 50],
                ['Fajitas de pollo', 160],
                ['Palomitas de pollo', 120],
                ['Juguito', 25],
            ],
        ],
        [
            'categoria' => 'Bebidas',
            'platillos' => [
                ['Coca cola', 45],
                ['Agua mineral', 45],
                ['Joya de sabor', 45],
                ['Agua natural', 40],
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

            foreach ($entry['platillos'] as [$dishName, $price]) {
                Dish::updateOrCreate(
                    [
                        'dish_category_id' => $category->id,
                        'name'             => $dishName,
                    ],
                    [
                        'price'       => $price,
                        'status'      => 'active',
                        'description' => null,
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
