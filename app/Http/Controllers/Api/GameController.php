<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Couple;
use App\Models\SwipeQuestion;
use App\Models\SwipeAnswer;
use App\Models\DrawingPrompt;
use App\Models\Drawing;
use App\Services\FcmService;

class GameController extends Controller
{
    private function getCoupleForUser($userId)
    {
        return Couple::where('user1_id', $userId)
            ->orWhere('user2_id', $userId)
            ->first();
    }

    public function getGameProgress(Request $request)
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);
        if (!$couple) return response()->json(['message' => 'No tienes pareja'], 403);

        $partnerId = $couple->user1_id === $user->id ? $couple->user2_id : $couple->user1_id;

        // 1. Preguntas (Questions)
        $totalQuestions = \App\Models\Question::count();
        $answeredQuestions = \App\Models\QuestionAnswer::where('user_id', $user->id)->count();
        $questionsPercent = $totalQuestions > 0 ? round(($answeredQuestions / $totalQuestions) * 100) : 0;
        $partnerQuestions = \App\Models\QuestionAnswer::where('user_id', $partnerId)->pluck('question_id')->toArray();
        $myQuestions = \App\Models\QuestionAnswer::where('user_id', $user->id)->pluck('question_id')->toArray();
        $pendingQuestions = count(array_diff($partnerQuestions, $myQuestions));

        // 2. Swipe Game
        $totalSwipe = SwipeQuestion::count();
        $answeredSwipe = SwipeAnswer::where('user_id', $user->id)->count();
        $swipePercent = $totalSwipe > 0 ? round(($answeredSwipe / $totalSwipe) * 100) : 0;
        $partnerSwipe = SwipeAnswer::where('user_id', $partnerId)->pluck('swipe_question_id')->toArray();
        $mySwipe = SwipeAnswer::where('user_id', $user->id)->pluck('swipe_question_id')->toArray();
        $pendingSwipe = count(array_diff($partnerSwipe, $mySwipe));

        // 3. Drawing Game
        $totalDrawing = DrawingPrompt::count();
        $answeredDrawing = Drawing::where('user_id', $user->id)->count();
        $drawingPercent = $totalDrawing > 0 ? round(($answeredDrawing / $totalDrawing) * 100) : 0;
        $partnerDrawingIds = Drawing::where('user_id', $partnerId)->pluck('drawing_prompt_id')->toArray();
        $myDrawingIds = Drawing::where('user_id', $user->id)->pluck('drawing_prompt_id')->toArray();
        $pendingDrawing = count(array_diff($partnerDrawingIds, $myDrawingIds));

        $totalPending = $pendingQuestions + $pendingSwipe + $pendingDrawing;

        return response()->json([
            'total_pending' => $totalPending,
            'questions' => [
                'total' => $totalQuestions,
                'answered' => $answeredQuestions,
                'percentage' => $questionsPercent,
                'pending_actions' => $pendingQuestions
            ],
            'swipe' => [
                'total' => $totalSwipe,
                'answered' => $answeredSwipe,
                'percentage' => $swipePercent,
                'pending_actions' => $pendingSwipe
            ],
            'drawing' => [
                'total' => $totalDrawing,
                'answered' => $answeredDrawing,
                'percentage' => $drawingPercent,
                'pending_actions' => $pendingDrawing
            ]
        ]);
    }

    // --- SWIPE GAME ---

    public function getSwipeCategories()
    {
        $categories = SwipeQuestion::select('category')->distinct()->pluck('category');
        return response()->json($categories);
    }

    public function getSwipeCards(Request $request)
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);
        if (!$couple) return response()->json(['message' => 'No tienes pareja'], 403);

        $category = $request->query('category');

        // Preguntas no respondidas por MÍ
        $answeredIds = SwipeAnswer::where('user_id', $user->id)->pluck('swipe_question_id')->toArray();
        
        $query = SwipeQuestion::whereNotIn('id', $answeredIds);
        if ($category) {
            $query->where('category', $category);
        }

        $pending = $query->inRandomOrder()->limit(10)->get();

        return response()->json($pending);
    }

    public function answerSwipe(Request $request)
    {
        $request->validate([
            'question_id' => 'required|exists:lovewidget_swipe_questions,id',
            'answer' => 'required|boolean'
        ]);

        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);
        if (!$couple) return response()->json(['message' => 'No tienes pareja'], 403);

        $answer = SwipeAnswer::updateOrCreate(
            ['user_id' => $user->id, 'swipe_question_id' => $request->question_id],
            ['couple_id' => $couple->id, 'answer' => $request->answer]
        );

        // Notificar a la pareja (Limitado a 1 vez cada 2 horas para no hacer spam)
        $partnerId = ($couple->user1_id == $user->id) ? $couple->user2_id : $couple->user1_id;
        $partner = \App\Models\User::find($partnerId);
        
        if ($partner && $partner->fcm_token) {
            $cacheKey = "swipe_notification_{$user->id}_{$partnerId}";
            
            if (!\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                $fcm = new FcmService();
                $fcm->sendToToken(
                    $partner->fcm_token,
                    "Tinder de Pareja 🔥",
                    "{$user->name} está respondiendo cartas. ¡Entra para ver si tenéis coincidencias!"
                );
                
                // Evitamos volver a enviar durante 2 horas
                \Illuminate\Support\Facades\Cache::put($cacheKey, true, now()->addHours(2));
            }
        }

        return response()->json(['message' => 'Respuesta guardada', 'data' => $answer]);
    }

    public function getAllSwipeCards(Request $request)
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);
        if (!$couple) return response()->json(['message' => 'No tienes pareja'], 403);

        $partnerId = $couple->user1_id === $user->id ? $couple->user2_id : $couple->user1_id;

        $questions = SwipeQuestion::paginate(50);
        $answers = SwipeAnswer::where('couple_id', $couple->id)
            ->whereIn('swipe_question_id', $questions->pluck('id'))
            ->get();

        $result = [];
        foreach ($questions as $q) {
            $myAnswer = $answers->where('swipe_question_id', $q->id)->where('user_id', $user->id)->first();
            $partnerAnswer = $answers->where('swipe_question_id', $q->id)->where('user_id', $partnerId)->first();

            $status = 'unanswered';
            if ($myAnswer && !$partnerAnswer) $status = 'waiting_partner';
            if (!$myAnswer && $partnerAnswer) $status = 'waiting_you';
            if ($myAnswer && $partnerAnswer) $status = 'answered';

            $result[] = [
                'id' => $q->id,
                'category' => $q->category,
                'question_text' => $q->question_text,
                'status' => $status,
                'my_answer' => $myAnswer ? $myAnswer->answer : null,
                'partner_answer' => ($status === 'answered') ? $partnerAnswer->answer : null,
            ];
        }
        return response()->json([
            'data' => $result,
            'current_page' => $questions->currentPage(),
            'last_page' => $questions->lastPage(),
            'total' => $questions->total()
        ]);
    }

    public function getSwipeStats(Request $request)
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);
        if (!$couple) return response()->json(['message' => 'No tienes pareja'], 403);

        $partnerId = $couple->user1_id === $user->id ? $couple->user2_id : $couple->user1_id;
        $category = $request->query('category');

        $myAnswersQuery = SwipeAnswer::where('user_id', $user->id)->with('question');
        $partnerAnswersQuery = SwipeAnswer::where('user_id', $partnerId);

        $myAnswers = $myAnswersQuery->get()->keyBy('swipe_question_id');
        $partnerAnswers = $partnerAnswersQuery->get()->keyBy('swipe_question_id');

        $matches = [];
        $mismatches = [];
        $totalCommon = 0;
        $agreed = 0;

        foreach ($myAnswers as $qId => $myAns) {
            if ($category && $myAns->question->category !== $category) continue;

            if (isset($partnerAnswers[$qId])) {
                $totalCommon++;
                $questionText = $myAns->question->question_text;
                $pAns = $partnerAnswers[$qId];

                $item = [
                    'question' => $questionText,
                    'my_answer' => $myAns->answer,
                    'partner_answer' => $pAns->answer
                ];

                if ($myAns->answer === $pAns->answer) {
                    $agreed++;
                    $matches[] = $item;
                } else {
                    $mismatches[] = $item;
                }
            }
        }

        $percentage = $totalCommon > 0 ? round(($agreed / $totalCommon) * 100) : 0;

        return response()->json([
            'percentage' => $percentage,
            'matches' => $matches,
            'mismatches' => $mismatches,
            'total_common' => $totalCommon
        ]);
    }

    // --- DRAWING GAME ---

    public function getDrawingCategories()
    {
        $categories = DrawingPrompt::select('category')->distinct()->pluck('category');
        return response()->json($categories);
    }

    public function getDrawingPrompt(Request $request)
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);
        if (!$couple) return response()->json(['message' => 'No tienes pareja'], 403);

        $partnerId = $couple->user1_id === $user->id ? $couple->user2_id : $couple->user1_id;
        $category = $request->query('category');

        // Buscar un prompt que la pareja haya empezado o respondido y yo no
        $partnerDrawings = Drawing::where('user_id', $partnerId)->pluck('drawing_prompt_id')->toArray();
        $myDrawings = Drawing::where('user_id', $user->id)->pluck('drawing_prompt_id')->toArray();

        $promptsPartnerDidButINot = array_diff($partnerDrawings, $myDrawings);
        
        $prompt = null;

        if (count($promptsPartnerDidButINot) > 0) {
            $query = DrawingPrompt::whereIn('id', $promptsPartnerDidButINot);
            if ($category) {
                $query->where('category', $category);
            }
            $prompt = $query->first();
        } 
        
        if (!$prompt) {
            // Buscar un prompt nuevo que ninguno haya hecho
            $allDone = array_merge($partnerDrawings, $myDrawings);
            $query = DrawingPrompt::whereNotIn('id', $allDone);
            if ($category) {
                $query->where('category', $category);
            }
            $prompt = $query->inRandomOrder()->first();
        }

        if (!$prompt) {
            return response()->json(['message' => 'No hay más retos de dibujo'], 404);
        }

        return response()->json($prompt);
    }

    public function uploadDrawing(Request $request)
    {
        $request->validate([
            'prompt_id' => 'required|exists:lovewidget_drawing_prompts,id',
            'image' => 'required|string' // base64
        ]);

        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);
        if (!$couple) return response()->json(['message' => 'No tienes pareja'], 403);

        $imageData = $request->image;
        $imageData = str_replace('data:image/png;base64,', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);
        $imageName = 'drawings/' . Str::random(10) . '_' . time() . '.png';
        
        Storage::disk('public')->put($imageName, base64_decode($imageData));

        $drawing = Drawing::updateOrCreate(
            ['user_id' => $user->id, 'drawing_prompt_id' => $request->prompt_id],
            ['couple_id' => $couple->id, 'image_path' => $imageName]
        );

        // Notificar a la pareja
        $partnerId = ($couple->user1_id == $user->id) ? $couple->user2_id : $couple->user1_id;
        $partner = \App\Models\User::find($partnerId);
        if ($partner && $partner->fcm_token) {
            $partnerDrawing = Drawing::where('user_id', $partnerId)->where('drawing_prompt_id', $request->prompt_id)->first();
            $fcm = new FcmService();
            if ($partnerDrawing) {
                $fcm->sendToToken(
                    $partner->fcm_token,
                    "¡Obras de arte listas! 🖼️",
                    "{$user->name} ha completado su dibujo. ¡Entra a ver el resultado!"
                );
            } else {
                $fcm->sendToToken(
                    $partner->fcm_token,
                    "¡Nuevo reto de dibujo! 🎨",
                    "{$user->name} ha dibujado. ¡Te toca a ti para poder verlo!"
                );
            }
        }

        return response()->json(['message' => 'Dibujo guardado', 'data' => $drawing]);
    }

    public function getDrawingResult($promptId)
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);
        if (!$couple) return response()->json(['message' => 'No tienes pareja'], 403);

        $partnerId = $couple->user1_id === $user->id ? $couple->user2_id : $couple->user1_id;

        $myDrawing = Drawing::where('user_id', $user->id)->where('drawing_prompt_id', $promptId)->first();
        $partnerDrawing = Drawing::where('user_id', $partnerId)->where('drawing_prompt_id', $promptId)->first();

        if (!$myDrawing) {
            return response()->json(['status' => 'pending_me', 'message' => 'Te falta dibujar']);
        }

        if (!$partnerDrawing) {
            return response()->json(['status' => 'waiting_partner', 'message' => 'Esperando a tu pareja']);
        }

        return response()->json([
            'status' => 'completed',
            'my_drawing' => $myDrawing->image_path,
            'partner_drawing' => $partnerDrawing->image_path,
            'prompt' => DrawingPrompt::find($promptId)->prompt_text
        ]);
    }

    public function getAllDrawingPrompts(Request $request)
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);
        if (!$couple) return response()->json(['message' => 'No tienes pareja'], 403);

        $partnerId = $couple->user1_id === $user->id ? $couple->user2_id : $couple->user1_id;

        $interactedPromptIds = Drawing::where('couple_id', $couple->id)
            ->distinct()
            ->pluck('drawing_prompt_id');

        $prompts = DrawingPrompt::whereIn('id', $interactedPromptIds)
            ->orderBy('id', 'desc')
            ->paginate(50);
            
        $drawings = Drawing::where('couple_id', $couple->id)
            ->whereIn('drawing_prompt_id', $prompts->pluck('id'))
            ->get();

        $result = [];
        foreach ($prompts as $p) {
            $myDrawing = $drawings->where('drawing_prompt_id', $p->id)->where('user_id', $user->id)->first();
            $partnerDrawing = $drawings->where('drawing_prompt_id', $p->id)->where('user_id', $partnerId)->first();

            $status = 'unanswered';
            if ($myDrawing && !$partnerDrawing) $status = 'waiting_partner';
            if (!$myDrawing && $partnerDrawing) $status = 'waiting_you';
            if ($myDrawing && $partnerDrawing) $status = 'completed';

            $result[] = [
                'id' => $p->id,
                'category' => $p->category,
                'prompt_text' => $p->prompt_text,
                'status' => $status,
                'my_drawing' => $myDrawing ? $myDrawing->image_path : null,
                'partner_drawing' => ($status === 'completed') ? $partnerDrawing->image_path : null,
            ];
        }

        return response()->json([
            'data' => $result,
            'current_page' => $prompts->currentPage(),
            'last_page' => $prompts->lastPage(),
            'total' => $prompts->total()
        ]);
    }
}
