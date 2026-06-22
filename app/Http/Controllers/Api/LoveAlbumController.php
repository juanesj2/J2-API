<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Couple;
use App\Models\LovePhoto;
use App\Models\LoveAlbum;
use App\Models\LovePhotoReaction;
use App\Models\CoupleMilestone;
use App\Models\Question;
use App\Models\QuestionAnswer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\FcmService;

class LoveAlbumController extends Controller
{
    private function getCoupleForUser($userId)
    {
        return Couple::where('user1_id', $userId)
            ->orWhere('user2_id', $userId)
            ->first();
    }

    public function pair(Request $request)
    {
        $request->validate([
            'pairing_code' => 'required|string|size:6'
        ]);

        $user = Auth::user();

        // Limite 1: Verificar que el usuario no tiene pareja ya
        if ($this->getCoupleForUser($user->id)) {
            return response()->json(['message' => 'Ya estás vinculado a una pareja.'], 400);
        }

        // Limite 3: No emparejarse consigo mismo
        if (strtoupper($request->pairing_code) === strtoupper($user->pairing_code)) {
            return response()->json(['message' => 'No puedes vincularte contigo mismo.'], 400);
        }

        // Buscar pareja por código
        $partner = \App\Models\User::where('pairing_code', strtoupper($request->pairing_code))->first();

        if (!$partner) {
            return response()->json(['message' => 'Código de vinculación inválido.'], 404);
        }

        // Limite 2: Verificar que el dueño del código no tiene pareja ya
        if ($this->getCoupleForUser($partner->id)) {
            return response()->json(['message' => 'Ese usuario ya está vinculado a otra persona.'], 400);
        }

        // Todo correcto, crear el vínculo
        Couple::create([
            'user1_id' => $user->id,
            'user2_id' => $partner->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => '¡Vinculación exitosa!'], 200);
    }

    public function getCoupleInfo(Request $request)
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);

        if (!$couple) {
            return response()->json(['message' => 'No estás vinculado a ninguna pareja.'], 403);
        }

        // Return couple info plus partner's mood
        $partnerId = ($couple->user1_id == $user->id) ? $couple->user2_id : $couple->user1_id;
        $partner = \App\Models\User::find($partnerId);

        $localDateStr = $request->query('local_date');
        $today = $localDateStr ? \Carbon\Carbon::parse($localDateStr)->startOfDay() : \Carbon\Carbon::now()->startOfDay();

        // Control de ruptura de racha al vuelo
        $lastStreakDate = $couple->last_photo_date ? \Carbon\Carbon::parse($couple->last_photo_date)->startOfDay() : null;
        if ($lastStreakDate && $couple->current_streak > 0) {
            $diffInDays = $today->diffInDays($lastStreakDate);
            if ($diffInDays > 1) {
                // Racha rota (ayer nadie o sólo uno subió foto)
                $couple->current_streak = 0;
                $couple->save();
            }
        }

        // Estado de subidas de HOY
        $dateStr = $today->format('Y-m-d');
        $myPhotoToday = \App\Models\LovePhoto::where('couple_id', $couple->id)
                            ->where('user_id', $user->id)
                            ->whereDate('fecha_recuerdo', $dateStr)
                            ->exists();

        $partnerPhotoToday = \App\Models\LovePhoto::where('couple_id', $couple->id)
                            ->where('user_id', $partnerId)
                            ->whereDate('fecha_recuerdo', $dateStr)
                            ->exists();

        $unlockedAchievements = \App\Models\CoupleAchievement::where('couple_id', $couple->id)
            ->pluck('achievement_id')
            ->toArray();

        return response()->json([
            'my_id' => (string) $user->id,
            'partner_id' => (string) $partnerId,
            'couple' => $couple,
            'current_streak' => $couple->current_streak,
            'my_mood' => $user->current_mood,
            'partner_mood' => $partner ? $partner->current_mood : null,
            'my_birth_date' => $user->birth_date,
            'partner_birth_date' => $partner ? $partner->birth_date : null,
            'my_name' => $user->name,
            'partner_name' => $partner ? $partner->name : null,
            'my_avatar' => $user->avatar_url ? url('storage/' . $user->avatar_url) : null,
            'partner_avatar' => ($partner && $partner->avatar_url) ? url('storage/' . $partner->avatar_url) : null,
            'my_photo_today' => $myPhotoToday,
            'partner_photo_today' => $partnerPhotoToday,
            'unlocked_achievements' => $unlockedAchievements
        ]);
    }

    public function updateCoupleInfo(Request $request)
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);

        if (!$couple) {
            return response()->json(['message' => 'No estás vinculado a ninguna pareja.'], 403);
        }

        if ($request->has('relationship_start_date')) {
            $couple->relationship_start_date = $request->relationship_start_date;
            $couple->save();
        }

        if ($request->has('birth_date')) {
            $user->birth_date = $request->birth_date;
            $user->save();
        }

        if ($request->has('partner_birth_date')) {
            $partnerId = ($couple->user1_id == $user->id) ? $couple->user2_id : $couple->user1_id;
            $partner = \App\Models\User::find($partnerId);
            if ($partner) {
                $partner->birth_date = $request->partner_birth_date;
                $partner->save();
            }
        }

        if ($request->has('current_mood')) {
            $user->current_mood = $request->current_mood;
            $user->save();
            
            // Notify partner
            $partnerId = ($couple->user1_id == $user->id) ? $couple->user2_id : $couple->user1_id;
            $partner = \App\Models\User::find($partnerId);
            if ($partner && $partner->fcm_token) {
                $fcm = new FcmService();
                $fcm->sendToToken(
                    $partner->fcm_token,
                    "Cambio de humor 🎭",
                    "{$user->name} se siente ahora: {$request->current_mood}."
                );
            }
        }

        return response()->json(['message' => 'Información actualizada con éxito']);
    }

    public function poke()
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);

        if (!$couple) {
            return response()->json(['message' => 'No estás vinculado a ninguna pareja.'], 403);
        }

        $couple->last_poke_at = now();
        $couple->poke_count = $couple->poke_count + 1;
        $couple->save();

        // Notify partner
        $partnerId = ($couple->user1_id == $user->id) ? $couple->user2_id : $couple->user1_id;
        $partner = \App\Models\User::find($partnerId);
        if ($partner && $partner->fcm_token) {
            $fcm = new FcmService();

            // Verificar si tienen el logro Dedo Inquieto
            $hasSecretSpammer = \App\Models\CoupleAchievement::where('couple_id', $couple->id)
                ->where('achievement_id', 'secret_spammer')
                ->exists();

            if ($hasSecretSpammer) {
                $messages = [
                    "⚡ ¡SÚPER ZUMBIDO! {$user->name} te ha bombardeado ⚡",
                    "💥 {$user->name} está golpeando la pantalla por ti 💥",
                    "💖 ¡ZUMBIDO NIVEL DIOS de {$user->name}! 💖",
                    "🚀 {$user->name} ha mandado un zumbido supersónico 🚀"
                ];
                $title = "⚡ ¡SÚPER ZUMBIDO! ⚡";
            } else {
                $messages = [
                    "{$user->name} te echa de menos 🥺",
                    "{$user->name} está pensando en ti 💭",
                    "¡Alguien reclama tu atención! 👀",
                    "{$user->name} te manda un abracito virtual 🫂",
                    "¡Ring ring! {$user->name} te llama 📱"
                ];
                $title = "🔔 Zumbido de {$user->name}! 🔔";
            }

            $randomMessage = $messages[array_rand($messages)];
            $fcm->sendToToken(
                $partner->fcm_token,
                $title,
                $randomMessage
            );
        }

        return response()->json(['message' => 'Zumbido enviado', 'poke_count' => $couple->poke_count]);
    }

    public function remindStreak()
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);

        if (!$couple) {
            return response()->json(['message' => 'No estás vinculado a ninguna pareja.'], 403);
        }

        $partnerId = ($couple->user1_id == $user->id) ? $couple->user2_id : $couple->user1_id;
        $partner = \App\Models\User::find($partnerId);

        if ($partner && $partner->fcm_token) {
            $fcm = new FcmService();
            $messages = [
                "{$user->name} te recuerda activar la racha 🔥",
                "¡Que no se te pase la racha! {$user->name} está esperando tu foto 📸",
                "¡Sube tu recuerdo de hoy! {$user->name} no quiere perder la racha 🥺",
                "A {$user->name} le falta tu foto para mantener la racha viva 💖",
                "¡Rápido! {$user->name} te avisa que la racha está en peligro ⏳"
            ];
            $randomMessage = $messages[array_rand($messages)];
            $fcm->sendToToken(
                $partner->fcm_token,
                "¡Alerta de Racha! 🔥",
                $randomMessage
            );
        }

        return response()->json(['message' => 'Recordatorio enviado']);
    }

    public function customNotification(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);

        if (!$couple) {
            return response()->json(['message' => 'No estás vinculado a ninguna pareja.'], 403);
        }

        $partnerId = ($couple->user1_id == $user->id) ? $couple->user2_id : $couple->user1_id;
        $partner = \App\Models\User::find($partnerId);

        if ($partner && $partner->fcm_token) {
            $fcm = new FcmService();
            $fcm->sendToToken(
                $partner->fcm_token,
                $request->input('title'),
                $request->input('body')
            );
        }

        return response()->json(['message' => 'Notificación sorpresa enviada']);
    }

    public function assignPhotosToAlbum(Request $request, $albumId)
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);

        if (!$couple) {
            return response()->json(['message' => 'No estás vinculado a ninguna pareja.'], 403);
        }

        $request->validate([
            'photo_ids' => 'required|array',
            'photo_ids.*' => 'integer'
        ]);

        // Verificamos que el álbum pertenezca a la pareja
        $album = LoveAlbum::where('id', $albumId)->where('couple_id', $couple->id)->first();
        if (!$album) {
            return response()->json(['message' => 'Álbum no encontrado o no pertenece a tu pareja.'], 404);
        }

        $photosToCopy = LovePhoto::whereIn('id', $request->photo_ids)
            ->where('couple_id', $couple->id)
            ->get();

        foreach ($photosToCopy as $photo) {
            LovePhoto::create([
                'couple_id' => $photo->couple_id,
                'user_id' => $photo->user_id,
                'album_id' => $albumId,
                'image_path' => $photo->image_path,
                'description' => $photo->description,
                'fecha_recuerdo' => $photo->fecha_recuerdo,
                'created_at' => $photo->created_at,
                'updated_at' => now(),
            ]);
        }

        return response()->json(['message' => 'Fotos copiadas al álbum con éxito']);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);

        if (!$couple) {
            return response()->json(['message' => 'No estás vinculado a ninguna pareja.'], 403);
        }

        $query = LovePhoto::where('couple_id', $couple->id)
            ->with(['user:id,name,email', 'reactions.user:id,name']);

        if ($request->has('album_id')) {
            $query->where('album_id', $request->album_id);
        } else {
            $query->where(function ($q) {
                $q->whereNull('description')
                  ->orWhere(function ($sub) {
                      $sub->where('description', 'NOT LIKE', '[DOODLE]%')
                          ->where('description', 'NOT LIKE', '[AUDIO]%')
                          ->where('description', 'NOT LIKE', '[GIF]%')
                          ->where('description', 'NOT LIKE', '[GRAFFITI:%');
                  });
            });
        }

        $photos = $query->orderBy('fecha_recuerdo', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($photos);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);

        if (!$couple) {
            return response()->json(['message' => 'No estás vinculado a ninguna pareja.'], 403);
        }

        $request->validate([
            // Usamos 'file' en lugar de 'image' por si el servidor (AlwaysData)
            // no tiene activa la extensión 'fileinfo' que Laravel usa para verificar mimes.
            // Aumentamos el límite a 15MB (15360 KB) para móviles modernos.
            'image' => 'required|file|max:15360',
            'description' => 'nullable|string|max:500',
            'fecha_recuerdo' => 'nullable|date',
            'album_id' => 'nullable|exists:love_albums,id'
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $isGraffiti = str_contains($request->description ?? '', '[GRAFFITI:');
            $extension = $isGraffiti ? '.png' : '.jpg';
            $filename = uniqid() . $extension;
            $imagePath = 'love_album/' . $filename;
            
            // Optimizar imagen
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($file);
            // Redimensionar si es muy grande, manteniendo proporciones
            $image->scaleDown(width: 1920, height: 1920);
            
            // Guardar imagen
            if ($isGraffiti) {
                \Illuminate\Support\Facades\Storage::disk('public')->put($imagePath, $image->toPng()->toString());
            } else {
                \Illuminate\Support\Facades\Storage::disk('public')->put($imagePath, $image->toJpeg(90)->toString());
            }

            // --- Calcular racha dual ---
            $localDateStr = $request->input('local_date');
            $today = $localDateStr ? \Carbon\Carbon::parse($localDateStr)->startOfDay() : \Carbon\Carbon::now()->startOfDay();
            $dateStr = $today->format('Y-m-d');
            
            $lastStreakDate = $couple->last_photo_date ? \Carbon\Carbon::parse($couple->last_photo_date)->startOfDay() : null;
            $partnerId = ($couple->user1_id == $user->id) ? $couple->user2_id : $couple->user1_id;

            $partnerPhotoToday = LovePhoto::where('couple_id', $couple->id)
                                          ->where('user_id', $partnerId)
                                          ->whereDate('fecha_recuerdo', $dateStr)
                                          ->exists();

            if ($partnerPhotoToday) {
                // AMBOS han subido hoy
                if (!$lastStreakDate) {
                    $couple->current_streak = 1;
                    $couple->longest_streak = 1;
                    $couple->last_photo_date = $today->format('Y-m-d H:i:s');
                } else {
                    $diffInDays = $today->diffInDays($lastStreakDate);
                    if ($diffInDays == 1) {
                        // Racha se mantiene y sube
                        $couple->current_streak += 1;
                        if ($couple->current_streak > $couple->longest_streak) {
                            $couple->longest_streak = $couple->current_streak;
                        }
                        $couple->last_photo_date = $today->format('Y-m-d H:i:s');
                    } elseif ($diffInDays > 1) {
                        // Hubo un hueco (se rompió), reiniciar a 1
                        $couple->current_streak = 1;
                        $couple->last_photo_date = $today->format('Y-m-d H:i:s');
                    }
                }
            } else {
                // SÓLO YO he subido hoy. Comprobamos si la racha se rompió ayer.
                if ($lastStreakDate && $couple->current_streak > 0) {
                    $diffInDays = $today->diffInDays($lastStreakDate);
                    if ($diffInDays > 1) {
                        // Ayer la racha murió
                        $couple->current_streak = 0;
                    }
                }
            }
            
            $couple->save();

            $photo = LovePhoto::create([
                'couple_id' => $couple->id,
                'user_id' => $user->id,
                'album_id' => $request->album_id,
                'image_path' => $imagePath,
                'description' => $request->description,
                'fecha_recuerdo' => $localDateStr ? $today->format('Y-m-d H:i:s') : now(),
            ]);

            // Si al guardar mi foto, el partner también lo hizo, la racha está completa hoy.
            // Para la respuesta JSON, lo incluimos.
            return response()->json([
                'message' => 'Foto subida con éxito',
                'photo' => $photo->load('user:id,name,email'),
                'streak' => $couple->current_streak,
                'my_photo_today' => true,
                'partner_photo_today' => $partnerPhotoToday
            ], 201);
        }

        return response()->json(['message' => 'Falta la imagen'], 400);
    }

    public function react(Request $request, $id)
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);

        if (!$couple) {
            return response()->json(['message' => 'No estás vinculado a ninguna pareja.'], 403);
        }

        $photo = LovePhoto::where('couple_id', $couple->id)->find($id);

        if (!$photo) {
            return response()->json(['message' => 'Foto no encontrada'], 404);
        }

        $request->validate([
            'content' => 'required|string|max:50'
        ]);

        $reaction = LovePhotoReaction::create([
            'love_photo_id' => $photo->id,
            'user_id' => $user->id,
            'content' => $request->content
        ]);

        return response()->json(['message' => 'Reacción añadida', 'reaction' => $reaction->load('user:id,name')]);
    }

    public function show($id)
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);

        if (!$couple) {
            return response()->json(['message' => 'No estás vinculado a ninguna pareja.'], 403);
        }

        $photo = LovePhoto::where('couple_id', $couple->id)
            ->with(['user:id,name,email', 'reactions.user:id,name'])
            ->find($id);

        if (!$photo) {
            return response()->json(['message' => 'Foto no encontrada'], 404);
        }

        return response()->json($photo);
    }

    public function download($id)
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);

        if (!$couple) {
            return response()->json(['message' => 'No estás vinculado a ninguna pareja.'], 403);
        }

        $photo = LovePhoto::where('couple_id', $couple->id)->findOrFail($id);
        $path = storage_path('app/public/' . $photo->image_path);

        if (!file_exists($path)) {
            return response()->json(['message' => 'Archivo no encontrado'], 404);
        }

        return response()->download($path, 'love_photo_' . $photo->id . '.jpg');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);

        if (!$couple) {
            return response()->json(['message' => 'No estás vinculado a ninguna pareja.'], 403);
        }

        $photo = LovePhoto::where('couple_id', $couple->id)->find($id);

        if (!$photo) {
            return response()->json(['message' => 'Foto no encontrada'], 404);
        }

        // --- STREAK DECREMENT LOGIC ---
        $today = \Carbon\Carbon::now()->startOfDay();
        $photoDate = \Carbon\Carbon::parse($photo->fecha_recuerdo)->startOfDay();

        if ($photoDate->equalTo($today)) {
            $remainingPhotosToday = LovePhoto::where('couple_id', $couple->id)
                ->where('user_id', $user->id)
                ->whereDate('fecha_recuerdo', $today->format('Y-m-d'))
                ->where('id', '!=', $photo->id)
                ->count();

            if ($remainingPhotosToday == 0) {
                $lastStreakDate = $couple->last_photo_date ? \Carbon\Carbon::parse($couple->last_photo_date)->startOfDay() : null;
                
                if ($lastStreakDate && $lastStreakDate->equalTo($today)) {
                    $couple->current_streak = max(0, $couple->current_streak - 1);
                    // Si la racha es 0, dejamos la fecha a null o ayer. La dejaremos a null si es 0.
                    if ($couple->current_streak == 0) {
                        $couple->last_photo_date = null;
                    } else {
                        $couple->last_photo_date = $today->copy()->subDay()->format('Y-m-d H:i:s');
                    }
                    $couple->save();
                }
            }
        }

        if (Storage::disk('public')->exists($photo->image_path)) {
            Storage::disk('public')->delete($photo->image_path);
        }

        $photo->delete();

        return response()->json(['message' => 'Foto eliminada con éxito']);
    }

    // --- ÁLBUMES PERSONALIZADOS ---
    public function getAlbums()
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);

        if (!$couple) {
            return response()->json(['message' => 'No estás vinculado a ninguna pareja.'], 403);
        }

        $albums = LoveAlbum::where('couple_id', $couple->id)
            ->withCount('photos')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($albums);
    }

    public function createAlbum(Request $request)
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);

        if (!$couple) {
            return response()->json(['message' => 'No estás vinculado a ninguna pareja.'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $album = LoveAlbum::create([
            'couple_id' => $couple->id,
            'name' => $request->name,
            'cover_image' => null // Placeholder
        ]);

        return response()->json(['message' => 'Álbum creado', 'album' => $album], 201);
    }

    public function updateAlbumCover(Request $request, $id)
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);
        if (!$couple) return response()->json([], 403);

        $album = LoveAlbum::where('couple_id', $couple->id)->find($id);
        if (!$album) return response()->json([], 404);

        $request->validate(['image' => 'required|string']);

        $imageParts = explode(";base64,", $request->image);
        if (count($imageParts) >= 2) {
            $imageBase64 = base64_decode($imageParts[1]);
            $fileName = uniqid() . '.png';
            $path = 'love_album/covers/' . $fileName;
            Storage::disk('public')->put($path, $imageBase64);

            $album->cover_image = $path;
            $album->save();
        }

        return response()->json(['message' => 'Portada actualizada', 'album' => $album]);
    }

    // --- HITOS IMPORTANTES (MILESTONES) ---
    public function getMilestones()
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);
        if (!$couple) return response()->json([]);

        $milestones = CoupleMilestone::where('couple_id', $couple->id)
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($m) {
                if ($m->image_url) {
                    $m->image_url_full = asset('storage/' . $m->image_url);
                }
                return $m;
            });

        return response()->json($milestones);
    }

    public function addMilestone(Request $request)
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);

        if (!$couple) return response()->json(['message' => 'Sin pareja'], 403);

        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date'
        ]);

        $milestone = CoupleMilestone::create([
            'couple_id' => $couple->id,
            'title' => $request->title,
            'date' => $request->date
        ]);

        return response()->json($milestone);
    }

    public function updateMilestone(Request $request, $id)
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);

        if (!$couple) return response()->json(['message' => 'Sin pareja'], 403);

        $milestone = CoupleMilestone::where('couple_id', $couple->id)->find($id);

        if (!$milestone) {
            return response()->json(['message' => 'Hito no encontrado'], 404);
        }

        $request->validate([
            'image' => 'nullable|file|max:15360',
            'story' => 'nullable|string|max:1000'
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('milestones', $filename, 'public');
            
            // Delete old image if exists
            if ($milestone->image_url && \Storage::disk('public')->exists($milestone->image_url)) {
                \Storage::disk('public')->delete($milestone->image_url);
            }
            
            $milestone->image_url = $path;
        }

        if ($request->has('story')) {
            $milestone->story = $request->story;
        }

        $milestone->save();

        if ($milestone->image_url) {
            $milestone->image_url_full = asset('storage/' . $milestone->image_url);
        }

        return response()->json($milestone);
    }

    public function deleteMilestone($id)
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);
        if (!$couple) return response()->json([], 403);

        $milestone = CoupleMilestone::where('couple_id', $couple->id)->find($id);
        if ($milestone) {
            $milestone->delete();
        }

        return response()->json(['message' => 'Hito eliminado']);
    }

    // --- MINIJUEGO DE PREGUNTAS ---
    public function getQuestions()
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);
        if (!$couple) return response()->json([], 403);

        // Fetch all questions and check if answered
        $questions = Question::all();
        $answers = QuestionAnswer::where('couple_id', $couple->id)->get();

        $result = [];
        $partnerId = ($couple->user1_id == $user->id) ? $couple->user2_id : $couple->user1_id;

        foreach ($questions as $q) {
            $myAnswer = $answers->where('question_id', $q->id)->where('user_id', $user->id)->first();
            $partnerAnswer = $answers->where('question_id', $q->id)->where('user_id', $partnerId)->first();

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
                'partner_answer' => ($status === 'answered') ? $partnerAnswer->answer : null, // Hidden until both answer
            ];
        }

        return response()->json($result);
    }

    public function answerQuestion(Request $request, $id)
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);
        if (!$couple) return response()->json([], 403);

        $request->validate(['answer' => 'required|string']);

        $answer = QuestionAnswer::updateOrCreate(
            ['couple_id' => $couple->id, 'user_id' => $user->id, 'question_id' => $id],
            ['answer' => $request->answer]
        );

        // Notify partner that I answered
        $partnerId = ($couple->user1_id == $user->id) ? $couple->user2_id : $couple->user1_id;
        $partner = \App\Models\User::find($partnerId);
        if ($partner && $partner->fcm_token) {
            $fcm = new FcmService();
            $fcm->sendToToken(
                $partner->fcm_token,
                "¡Nueva respuesta! 👀",
                "{$user->name} ha respondido una pregunta. ¡Te toca a ti!"
            );
        }

        return response()->json(['message' => 'Respuesta guardada']);
    }

    public function saveFcmToken(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->fcm_token = $request->input('token');
            $user->save();
        }
        return response()->json(['success' => true]);
    }
    
    public function uploadAvatar(Request $request)
    {
        $request->validate(['avatar' => 'required|string']);
        $user = Auth::user();
        
        // Extraer base64 si viene como data URI
        $imgData = $request->avatar;
        if (preg_match('/^data:image\/(\w+);base64,/', $imgData, $type)) {
            $imgData = substr($imgData, strpos($imgData, ',') + 1);
            $type = strtolower($type[1]);
            if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif'])) {
                $type = 'jpg';
            }
        } else {
            $type = 'jpg';
        }
        
        $imgData = str_replace(' ', '+', $imgData);
        $imageName = 'user_' . $user->id . '_' . time() . '.' . $type;
        
        Storage::disk('public')->put('avatars/' . $imageName, base64_decode($imgData));
        
        $user->avatar_url = 'avatars/' . $imageName;
        $user->save();
        
        return response()->json(['success' => true, 'avatar_url' => url('storage/' . $user->avatar_url)]);
    }

    public function getRouletteOptions(Request $request)
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);
        if (!$couple) return response()->json(['options' => []]);

        $options = \App\Models\CoupleRouletteOption::where('couple_id', $couple->id)
                    ->orderBy('id')
                    ->pluck('title');
                    
        return response()->json(['options' => $options]);
    }

    public function updateRouletteOptions(Request $request)
    {
        $request->validate(['options' => 'array']);
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);
        if (!$couple) return response()->json(['message' => 'No estás vinculado a ninguna pareja.'], 403);

        \App\Models\CoupleRouletteOption::where('couple_id', $couple->id)->delete();

        if ($request->options) {
            foreach ($request->options as $opt) {
                \App\Models\CoupleRouletteOption::create([
                    'couple_id' => $couple->id,
                    'title' => $opt
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    // --- WISHES (CUBO DE DESEOS) ---
    public function getWishes()
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);
        if (!$couple) return response()->json([], 403);

        $wishes = \App\Models\Wish::where('couple_id', $couple->id)->orderBy('created_at', 'desc')->get();
        return response()->json($wishes);
    }

    public function addWish(Request $request)
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);
        if (!$couple) return response()->json([], 403);

        $wish = \App\Models\Wish::create([
            'couple_id' => $couple->id,
            'title' => $request->title,
            'completed' => false,
        ]);

        return response()->json($wish, 201);
    }

    public function updateWish(Request $request, $id)
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);
        if (!$couple) return response()->json([], 403);

        $wish = \App\Models\Wish::where('couple_id', $couple->id)->where('id', $id)->first();
        if ($wish) {
            $wish->completed = $request->completed;
            $wish->save();
        }

        return response()->json($wish);
    }

    public function deleteWish($id)
    {
        $user = Auth::user();
        $couple = $this->getCoupleForUser($user->id);
        if (!$couple) return response()->json([], 403);

        $wish = \App\Models\Wish::where('couple_id', $couple->id)->where('id', $id)->first();
        if ($wish) $wish->delete();
        
        return response()->json(['message' => 'Deseo eliminado']);
    }
}
