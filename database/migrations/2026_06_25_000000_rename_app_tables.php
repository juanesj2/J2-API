<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Enfoca
        if (Schema::hasTable('comentarios')) Schema::rename('comentarios', 'enfoca_comentarios');
        if (Schema::hasTable('desafios')) Schema::rename('desafios', 'enfoca_desafios');
        if (Schema::hasTable('fotografias')) Schema::rename('fotografias', 'enfoca_fotografias');
        if (Schema::hasTable('grupos')) Schema::rename('grupos', 'enfoca_grupos');
        if (Schema::hasTable('grupo_mensajes')) Schema::rename('grupo_mensajes', 'enfoca_grupo_mensajes');
        if (Schema::hasTable('likes')) Schema::rename('likes', 'enfoca_likes');
        if (Schema::hasTable('reportes')) Schema::rename('reportes', 'enfoca_reportes');

        // Love Widget
        if (Schema::hasTable('achievements')) Schema::rename('achievements', 'lovewidget_achievements');
        if (Schema::hasTable('couple_achievements')) Schema::rename('couple_achievements', 'lovewidget_couple_achievements');
        if (Schema::hasTable('couple_food_dishes')) Schema::rename('couple_food_dishes', 'lovewidget_couple_food_dishes');
        if (Schema::hasTable('couple_food_places')) Schema::rename('couple_food_places', 'lovewidget_couple_food_places');
        if (Schema::hasTable('couple_messages')) Schema::rename('couple_messages', 'lovewidget_couple_messages');
        if (Schema::hasTable('couple_milestones')) Schema::rename('couple_milestones', 'lovewidget_couple_milestones');
        if (Schema::hasTable('couple_movies')) Schema::rename('couple_movies', 'lovewidget_couple_movies');
        if (Schema::hasTable('couple_roulette_options')) Schema::rename('couple_roulette_options', 'lovewidget_couple_roulette_options');
        if (Schema::hasTable('couple_unlocked_hints')) Schema::rename('couple_unlocked_hints', 'lovewidget_couple_unlocked_hints');
        if (Schema::hasTable('drawings')) Schema::rename('drawings', 'lovewidget_drawings');
        if (Schema::hasTable('drawing_prompts')) Schema::rename('drawing_prompts', 'lovewidget_drawing_prompts');
        if (Schema::hasTable('love_albums')) Schema::rename('love_albums', 'lovewidget_love_albums');
        if (Schema::hasTable('love_photos')) Schema::rename('love_photos', 'lovewidget_love_photos');
        if (Schema::hasTable('love_photo_reactions')) Schema::rename('love_photo_reactions', 'lovewidget_love_photo_reactions');
        if (Schema::hasTable('questions')) Schema::rename('questions', 'lovewidget_questions');
        if (Schema::hasTable('question_answers')) Schema::rename('question_answers', 'lovewidget_question_answers');
        if (Schema::hasTable('swipe_answers')) Schema::rename('swipe_answers', 'lovewidget_swipe_answers');
        if (Schema::hasTable('swipe_questions')) Schema::rename('swipe_questions', 'lovewidget_swipe_questions');
        if (Schema::hasTable('wishes')) Schema::rename('wishes', 'lovewidget_wishes');
        if (Schema::hasTable('couples')) Schema::rename('couples', 'lovewidget_couples');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Enfoca
        if (Schema::hasTable('enfoca_comentarios')) Schema::rename('enfoca_comentarios', 'comentarios');
        if (Schema::hasTable('enfoca_desafios')) Schema::rename('enfoca_desafios', 'desafios');
        if (Schema::hasTable('enfoca_fotografias')) Schema::rename('enfoca_fotografias', 'fotografias');
        if (Schema::hasTable('enfoca_grupos')) Schema::rename('enfoca_grupos', 'grupos');
        if (Schema::hasTable('enfoca_grupo_mensajes')) Schema::rename('enfoca_grupo_mensajes', 'grupo_mensajes');
        if (Schema::hasTable('enfoca_likes')) Schema::rename('enfoca_likes', 'likes');
        if (Schema::hasTable('enfoca_reportes')) Schema::rename('enfoca_reportes', 'reportes');

        // Love Widget
        if (Schema::hasTable('lovewidget_achievements')) Schema::rename('lovewidget_achievements', 'achievements');
        if (Schema::hasTable('lovewidget_couple_achievements')) Schema::rename('lovewidget_couple_achievements', 'couple_achievements');
        if (Schema::hasTable('lovewidget_couple_food_dishes')) Schema::rename('lovewidget_couple_food_dishes', 'couple_food_dishes');
        if (Schema::hasTable('lovewidget_couple_food_places')) Schema::rename('lovewidget_couple_food_places', 'couple_food_places');
        if (Schema::hasTable('lovewidget_couple_messages')) Schema::rename('lovewidget_couple_messages', 'couple_messages');
        if (Schema::hasTable('lovewidget_couple_milestones')) Schema::rename('lovewidget_couple_milestones', 'couple_milestones');
        if (Schema::hasTable('lovewidget_couple_movies')) Schema::rename('lovewidget_couple_movies', 'couple_movies');
        if (Schema::hasTable('lovewidget_couple_roulette_options')) Schema::rename('lovewidget_couple_roulette_options', 'couple_roulette_options');
        if (Schema::hasTable('lovewidget_couple_unlocked_hints')) Schema::rename('lovewidget_couple_unlocked_hints', 'couple_unlocked_hints');
        if (Schema::hasTable('lovewidget_drawings')) Schema::rename('lovewidget_drawings', 'drawings');
        if (Schema::hasTable('lovewidget_drawing_prompts')) Schema::rename('lovewidget_drawing_prompts', 'drawing_prompts');
        if (Schema::hasTable('lovewidget_love_albums')) Schema::rename('lovewidget_love_albums', 'love_albums');
        if (Schema::hasTable('lovewidget_love_photos')) Schema::rename('lovewidget_love_photos', 'love_photos');
        if (Schema::hasTable('lovewidget_love_photo_reactions')) Schema::rename('lovewidget_love_photo_reactions', 'love_photo_reactions');
        if (Schema::hasTable('lovewidget_questions')) Schema::rename('lovewidget_questions', 'questions');
        if (Schema::hasTable('lovewidget_question_answers')) Schema::rename('lovewidget_question_answers', 'question_answers');
        if (Schema::hasTable('lovewidget_swipe_answers')) Schema::rename('lovewidget_swipe_answers', 'swipe_answers');
        if (Schema::hasTable('lovewidget_swipe_questions')) Schema::rename('lovewidget_swipe_questions', 'swipe_questions');
        if (Schema::hasTable('lovewidget_wishes')) Schema::rename('lovewidget_wishes', 'wishes');
        if (Schema::hasTable('lovewidget_couples')) Schema::rename('lovewidget_couples', 'couples');
    }
};
