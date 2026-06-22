<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Desafio;

class DesafioSeeder extends Seeder
{
    public function run()
    {
        $desafios = [
            [
                'titulo' => 'Primer paso',
                'descripcion' => 'Has subido tu primera fotografía.',
                'icono' => 'folder-invoices.png',
            ],
            [
                'titulo' => 'Cinco capturas',
                'descripcion' => 'Comparte 5 fotos con la comunidad.',
                'icono' => 'crown.png',
            ],
            [
                'titulo' => 'Me gusta esto',
                'descripcion' => 'Recibiste tu primer like en una foto.',
                'icono' => 'error.png',
            ],
            [
                'titulo' => 'Popular',
                'descripcion' => 'Una de tus fotos recibió más de 25 likes.',
                'icono' => 'like--v1.png',
            ],
            [
                'titulo' => 'Social',
                'descripcion' => 'Has comentado en 10 fotos de otros usuarios.',
                'icono' => 'fire-element.png',
            ],
            [
                'titulo' => 'Coleccionista',
                'descripcion' => 'Has conseguido al menos 5 logros distintos.',
                'icono' => 'walking.png',
            ],
        ];

        foreach ($desafios as $desafio) {
            Desafio::Create($desafio);
        }
    }
}
