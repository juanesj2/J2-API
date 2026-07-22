<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SwipeQuestion;
use App\Models\Question;
use App\Models\DrawingPrompt;

class SpicyPackSeeder extends Seeder
{
    public function run()
    {
        // 1. Swipe Questions (60 items)
        $swipeQuestions = [
            "¿Alguna vez has tenido una fantasía conmigo en un lugar público?",
            "¿Te gusta que tome la iniciativa en la cama?",
            "¿Te excita que te hablen sucio?",
            "¿Harías un trío si yo te lo pidiera?",
            "¿Tienes alguna fantasía inconfesable?",
            "¿Te gusta usar juguetes en pareja?",
            "¿Te atreverías a hacerlo en la playa o en un bosque?",
            "¿Te excita ver cómo me toco?",
            "¿Alguna vez has pensado en mí mientras te tocabas?",
            "¿Te gustaría que probáramos juegos de roles (roleplay)?",
            "¿Crees que el tamaño importa?",
            "¿Te gustaría grabar un vídeo íntimo nuestro?",
            "¿Te pondría hacerlo frente a un espejo grande?",
            "¿Te gusta dejar marcas o que te las dejen (chupetones, arañazos)?",
            "¿Alguna vez te has excitado en un momento completamente inapropiado?",
            "¿Te gusta que haya luz encendida durante el sexo?",
            "¿Probarías el sexo anal si aún no lo has hecho?",
            "¿Te gustaría vendarme los ojos?",
            "¿Te gusta experimentar con diferentes temperaturas (hielo, cera)?",
            "¿Te gustaría atarme a la cama?",
            "¿Te excita que te miren mientras lo hacemos?",
            "¿Alguna vez has fantaseado con alguien famoso?",
            "¿Te gusta que te besen el cuello lentamente?",
            "¿Te gustaría probar hacerlo en el coche?",
            "¿Te atrae la idea del voyeurismo o exhibicionismo?",
            "¿Crees que tenemos buena química sexual?",
            "¿Te gustaría que te sorprendiera con lencería nueva?",
            "¿Te pondría que te enviara una foto subida de tono ahora mismo?",
            "¿Te gusta el sexo mañanero?",
            "¿Te gustaría hacerlo en un balcón o terraza?",
            "¿Te excita el ruido (gemidos fuertes) durante el acto?",
            "¿Has soñado alguna vez con tener relaciones en el trabajo?",
            "¿Te atreverías a jugar a algo íntimo en una fiesta con amigos cerca?",
            "¿Crees que el sexo de reconciliación es el mejor?",
            "¿Te gustaría que te despertara de una manera 'caliente'?",
            "¿Te gusta recibir masajes eróticos?",
            "¿Te atreverías a hacerlo en un baño público?",
            "¿Te excitan los besos inesperados?",
            "¿Te gusta la idea de usar disfraces en la cama?",
            "¿Has pensado alguna vez en hacerlo en un avión?",
            "¿Te excita que te digan lo que tienes que hacer?",
            "¿Te gustaría usar comida (nata, chocolate) en nuestro juego previo?",
            "¿Te pondría que te coja fuerte del pelo?",
            "¿Sientes curiosidad por el BDSM ligero?",
            "¿Te gustaría probar diferentes tipos de lubricantes (sabores, efecto calor)?",
            "¿Te excita el 'dirty talk' (palabras sucias)?",
            "¿Te atreverías a mandarme un audio gemiendo?",
            "¿Crees que duras lo suficiente en la cama?",
            "¿Te gustaría hacerlo en la ducha o bañera?",
            "¿Te excitan los chupetones?",
            "¿Te atreverías a enviarme un desnudo sin venir a cuento?",
            "¿Has fingido alguna vez un orgasmo conmigo?",
            "¿Te gustaría que te diera unos azotes?",
            "¿Te gustaría hacerlo viendo pornografía juntos?",
            "¿Te pondría hacerlo en una piscina por la noche?",
            "¿Te excita que use la lengua en lugares inesperados?",
            "¿Sientes curiosidad por usar vibradores juntos?",
            "¿Te atreverías a salir sin ropa interior a una cita?",
            "¿Te gusta que te susurre al oído mientras lo hacemos?",
            "¿Te pondría tener relaciones en el cine?"
        ];

        foreach ($swipeQuestions as $sq) {
            SwipeQuestion::updateOrCreate(
                ['question_text' => $sq],
                ['category' => 'Picante']
            );
        }

        // 2. Quiz Questions (50 items)
        $quizQuestions = [
            "¿Cuál es tu postura favorita y por qué?",
            "Si solo pudieras tocar una parte de mi cuerpo durante todo el día, ¿cuál elegirías?",
            "¿Cuál es la mayor locura que has hecho por sexo?",
            "Describe tu fantasía sexual más oscura y profunda.",
            "¿Cuál es el lugar más raro donde hemos tenido intimidad?",
            "¿Qué cosa que nunca hemos hecho te gustaría probar en la cama?",
            "¿Cuál fue la vez que más placer sentiste estando conmigo?",
            "¿Qué ropa interior te vuelve loco/a verme puesta?",
            "Si pudieras elegir un súper poder para usarlo en la cama, ¿cuál sería?",
            "¿Cuál es tu juguete sexual favorito (o cuál te gustaría tener)?",
            "¿Qué es lo que más te excita de mi cuerpo?",
            "¿Cómo describirías nuestro primer encuentro sexual?",
            "Si tuviéramos que grabarnos, ¿qué temática elegirías para el vídeo?",
            "¿Qué te gustaría que te hiciera esta misma noche?",
            "¿Alguna vez te has tocado pensando en algo específico que te hice?",
            "¿Qué palabra te excita más escuchar durante el sexo?",
            "¿Qué es lo que más te gusta que te hagan en los preliminares?",
            "Describe cómo sería tu mañana perfecta en la cama.",
            "¿Qué límite sexual tienes claro que nunca cruzarías?",
            "¿Qué te parece la idea del sexo en lugares públicos?",
            "Si estuviéramos en un probador de ropa, ¿qué harías?",
            "¿Cuál es el recuerdo sexual más intenso que tienes de nosotros?",
            "¿Qué es lo más sucio que te gustaría decirme pero no te atreves?",
            "¿Qué canción pondrías de fondo para una noche de pasión?",
            "¿Qué opinas sobre incorporar hielo o cambios de temperatura al sexo?",
            "¿Cómo te gusta que te toquen cuando estás muy excitado/a?",
            "¿Qué parte de tu cuerpo es la más sensible?",
            "¿Cuál es el lugar más arriesgado en el que te gustaría hacerlo?",
            "Si fueras a atarme, ¿qué sería lo primero que me harías?",
            "¿Qué es lo que más te gusta hacer con tu lengua?",
            "¿Alguna vez has tenido un sueño erótico conmigo muy vívido?",
            "¿Qué opinas de incluir a una tercera persona?",
            "¿Te gustaría que te sorprendiera en el trabajo o la universidad?",
            "¿Qué prenda mía te gustaría quitarme con los dientes?",
            "¿Qué es lo que más te pone de que tome el control?",
            "¿Alguna vez te has excitado viendo cómo me visto o desvisto?",
            "¿Qué opinas de los espejos en la habitación?",
            "¿Cómo te gustaría que te despertara en tu cumpleaños?",
            "¿Cuál es tu zona erógena menos conocida?",
            "¿Qué es lo más travieso que has hecho en tu vida?",
            "Si tuvieras que usar un alimento en la cama, ¿cuál sería?",
            "¿Te gusta más dominar o ser dominado/a?",
            "¿Qué opinas del voyeurismo (que nos vean o ver a otros)?",
            "¿Cuál es tu ritmo favorito: suave y romántico, o duro y salvaje?",
            "¿Qué te gustaría que te hiciera con las manos atadas?",
            "¿Alguna vez te has masturbado en el trabajo o clase?",
            "¿Qué es lo más pervertido que te gusta que te haga?",
            "¿Qué opinas del dirty talk extremo?",
            "¿Cómo reaccionarías si te pido que me pegues?",
            "¿Cuál es tu mayor fetiche inconfesable?"
        ];

        foreach ($quizQuestions as $qq) {
            Question::updateOrCreate(
                ['question_text' => $qq],
                ['category' => 'Picante']
            );
        }

        // 3. Drawing Prompts (50 items)
        $drawingPrompts = [
            "Dibuja la lencería que más te gusta",
            "Dibuja el juguete sexual que te gustaría que compráramos",
            "Dibuja tu postura favorita en monigotes",
            "Dibuja el lugar público donde te gustaría hacerlo",
            "Dibuja tu parte favorita de mi cuerpo",
            "Dibuja cómo te imaginas mi cara cuando llego al clímax",
            "Dibuja la escena de tu sueño erótico más salvaje",
            "Dibuja un consolador gigante",
            "Dibuja el lugar más raro donde lo hemos hecho",
            "Dibuja algo que te exite visualmente",
            "Dibujame atado/a a la cama",
            "Dibuja una escena de BDSM suave",
            "Dibuja lo que te gustaría hacerme en un probador",
            "Dibuja tu disfraz de roleplay ideal",
            "Dibuja un par de pechos",
            "Dibuja la lencería más atrevida que usarías",
            "Dibujame dándote placer oral",
            "Dibuja nuestra cama después de una noche salvaje",
            "Dibujame esposado/a",
            "Dibuja una fruta con forma sugerente",
            "Dibuja lo primero que harías si tuviéramos un espejo en el techo",
            "Dibuja un beso apasionado bajo la lluvia",
            "Dibuja tu juguete favorito actual",
            "Dibujame haciéndote un baile privado",
            "Dibuja un masaje con final feliz",
            "Dibuja algo que rime con 'falo'",
            "Dibuja la postura que más te cansa pero te gusta",
            "Dibuja un juguete anal",
            "Dibujate a ti mismo/a seduciéndome",
            "Dibuja una noche de hotel de lujo",
            "Dibuja lo que te gustaría hacerme en el ascensor",
            "Dibuja a un demonio sexy",
            "Dibuja esposas de peluche",
            "Dibuja un antifaz para los ojos",
            "Dibuja una fusta de cuero",
            "Dibuja una gota de sudor cayendo por mi cuello",
            "Dibuja cómo me muerdes el labio",
            "Dibujate dándome azotes",
            "Dibuja un chupetón",
            "Dibuja cómo me quitas la ropa íntima",
            "Dibuja un consolador doble",
            "Dibuja la ropa interior que llevas puesta (si llevas)",
            "Dibuja una escena de kamasutra en palitos",
            "Dibuja una fresa con nata",
            "Dibuja un helado muy sugerente",
            "Dibuja lo que haríamos en el asiento trasero del coche",
            "Dibuja un vibrador de conejo",
            "Dibuja un corsé",
            "Dibujate a punto de correrte",
            "Dibuja la palabra más sucia que se te ocurra"
        ];

        foreach ($drawingPrompts as $dp) {
            DrawingPrompt::updateOrCreate(
                ['prompt_text' => $dp],
                ['category' => 'Picante']
            );
        }
    }
}
