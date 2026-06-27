<?php

namespace App\Console\Commands;

use App\Models\Dish;
use App\Models\DishCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SeedMenuFromJs extends Command
{
    protected $signature = 'menu:seed-from-js {--file=database/seeders/menu-data.js : Ruta al archivo JS}';
    protected $description = 'Importa categorías y platillos desde el archivo menu-data.js';

    public function handle(): int
    {
        $filePath = base_path($this->option('file'));

        if (!file_exists($filePath)) {
            $this->error("Archivo no encontrado: {$filePath}");
            return self::FAILURE;
        }

        $content = file_get_contents($filePath);
        $menu    = $this->parseMenuJs($content);

        if (empty($menu)) {
            $this->error('No se pudo parsear el menú. Revisa el formato del archivo JS.');
            return self::FAILURE;
        }

        $categoriesCreated = 0;
        $categoriesUpdated = 0;
        $dishesCreated     = 0;
        $dishesUpdated     = 0;
        $order             = 1;

        foreach ($menu as $entry) {
            $categoryName = trim($entry['categoria']);
            $slug         = Str::slug($categoryName);

            $category = DishCategory::where('slug', $slug)->first();
            if ($category) {
                $category->update([
                    'name'          => $categoryName,
                    'display_order' => $order,
                    'status'        => 'active',
                ]);
                $categoriesUpdated++;
            } else {
                $category = DishCategory::create([
                    'name'          => $categoryName,
                    'slug'          => $slug,
                    'display_order' => $order,
                    'status'        => 'active',
                ]);
                $categoriesCreated++;
            }

            foreach ($entry['platillos'] as $platillo) {
                $name  = trim($platillo[0]);
                $price = (float) $platillo[1];

                $existing = Dish::where('dish_category_id', $category->id)
                                ->where('name', $name)
                                ->first();

                if ($existing) {
                    $existing->update(['price' => $price]);
                    $dishesUpdated++;
                } else {
                    Dish::create([
                        'dish_category_id' => $category->id,
                        'name'             => $name,
                        'price'            => $price,
                        'status'           => 'active',
                        'description'      => null,
                        'image_path'       => null,
                    ]);
                    $dishesCreated++;
                }
            }

            $order++;
        }

        $this->info("Categorías creadas: {$categoriesCreated}  |  actualizadas: {$categoriesUpdated}");
        $this->info("Platillos creados: {$dishesCreated}  |  actualizados: {$dishesUpdated}");

        return self::SUCCESS;
    }

    private function parseMenuJs(string $content): array
    {
        // Strip single-line comments (// ...) from every line
        $lines   = explode("\n", $content);
        $cleaned = [];
        foreach ($lines as $line) {
            $trimmed = trim($line);
            // Skip full-line comments
            if (str_starts_with($trimmed, '//')) {
                continue;
            }
            // Remove inline comments (after non-string content) — simplified: strip // not inside quotes
            $line    = $this->stripInlineComment($line);
            $cleaned[] = $line;
        }
        $content = implode("\n", $cleaned);

        // Extract the array literal from: const menu = [...];
        if (!preg_match('/const\s+menu\s*=\s*(\[[\s\S]*?\])\s*;/', $content, $matches)) {
            return [];
        }

        $arrayStr = $matches[1];

        // Convert JS object literal syntax to JSON
        // Step 1: Quote unquoted keys  (categoria:  platillos:)
        $arrayStr = preg_replace('/(\b[a-zA-Z_][a-zA-Z0-9_]*\b)\s*:/', '"$1":', $arrayStr);

        // Step 2: Remove trailing commas before ] or }
        $arrayStr = preg_replace('/,\s*([\]}])/', '$1', $arrayStr);

        $decoded = json_decode($arrayStr, true);

        if (!is_array($decoded)) {
            return [];
        }

        return $decoded;
    }

    private function stripInlineComment(string $line): string
    {
        $inString    = false;
        $stringChar  = '';
        $result      = '';
        $len         = strlen($line);

        for ($i = 0; $i < $len; $i++) {
            $char = $line[$i];

            if ($inString) {
                $result .= $char;
                if ($char === '\\') {
                    // Escape next char
                    $i++;
                    if ($i < $len) {
                        $result .= $line[$i];
                    }
                } elseif ($char === $stringChar) {
                    $inString = false;
                }
            } else {
                if ($char === '"' || $char === "'") {
                    $inString   = true;
                    $stringChar = $char;
                    $result    .= $char;
                } elseif ($char === '/' && ($i + 1 < $len) && $line[$i + 1] === '/') {
                    // Inline comment starts — stop here
                    break;
                } else {
                    $result .= $char;
                }
            }
        }

        return $result;
    }
}
