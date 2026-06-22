<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;

class QuestionSeeder extends Seeder
{
    public function run()
    {
        $questions = [
            ['category' => 'Viajes', 'question_text' => '¿Cuál sería tu destino ideal para unas vacaciones de un mes?'],
            ['category' => 'Viajes', 'question_text' => '¿Prefieres un viaje de mochilero o un resort todo incluido?'],
            ['category' => 'Futuro', 'question_text' => '¿Dónde te ves viviendo en 10 años?'],
            ['category' => 'Futuro', 'question_text' => '¿Qué es lo más importante para ti en nuestra relación a largo plazo?'],
            ['category' => 'Convivencia', 'question_text' => '¿Cuál es la tarea del hogar que más odias hacer?'],
            ['category' => 'Convivencia', 'question_text' => '¿Qué pequeña manía mía te hace gracia (o te saca un poco de quicio)?'],
            ['category' => 'Familia', 'question_text' => '¿Cuántos hijos te gustaría tener o cómo te imaginas nuestra familia?'],
            ['category' => 'Intimidad', 'question_text' => '¿Cuál es tu recuerdo favorito de nosotros dos solos?'],
            ['category' => 'Diversión', 'question_text' => 'Si tuviéramos que ir a un reality show, ¿a cuál iríamos?'],
            ['category' => 'Diversión', 'question_text' => '¿Cuál es el mejor regalo que te he hecho?']
        ];

        foreach ($questions as $q) {
            Question::create($q);
        }
    }
}
