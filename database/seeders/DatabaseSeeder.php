<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            UserSeeder::class,
            GrupoSeeder::class,
            ReporteSeeder::class,
            DesafioSeeder::class,
            // Agregando las preguntas para el minijuego de amor
            QuestionSeeder::class,
        ]);

    }
}
