<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Couple;
use App\Models\CoupleMilestone;
use App\Services\FcmService;
use App\Models\User;
use Carbon\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $fcm = new FcmService();
    $couples = Couple::all();

    foreach ($couples as $couple) {
        $u1 = User::find($couple->user1_id);
        $u2 = User::find($couple->user2_id);

        if (!$u1 || !$u2) continue;

        // 1. STREAK REMINDER
        $u1UploadedToday = \App\Models\LovePhoto::where('couple_id', $couple->id)
            ->where('user_id', $u1->id)
            ->whereDate('created_at', now()->toDateString())
            ->exists();
            
        $u2UploadedToday = \App\Models\LovePhoto::where('couple_id', $couple->id)
            ->where('user_id', $u2->id)
            ->whereDate('created_at', now()->toDateString())
            ->exists();

        if (!$u1UploadedToday && $u1->fcm_token) {
            $fcm->sendToToken($u1->fcm_token, "🔥 ¡No rompas la racha! 🔥", "Sube un recuerdo de hoy y descubre si tu pareja también lo hizo.");
        }
        
        if (!$u2UploadedToday && $u2->fcm_token) {
            $fcm->sendToToken($u2->fcm_token, "🔥 ¡No rompas la racha! 🔥", "Sube un recuerdo de hoy y descubre si tu pareja también lo hizo.");
        }

        // 2. ANNIVERSARY REMINDER
        if ($couple->relationship_start_date) {
            $startDate = Carbon::parse($couple->relationship_start_date)->startOfDay();
            $tomorrow = now()->addDay()->startOfDay();

            if ($startDate->day === $tomorrow->day) {
                $months = $startDate->diffInMonths($tomorrow);
                if ($months > 0) {
                    $years = floor($months / 12);
                    $remainingMonths = $months % 12;
                    
                    $msg = "";
                    if ($years > 0 && $remainingMonths === 0) {
                        $msg = "¡Mañana hacéis {$years} año(s)! 🎉";
                    } elseif ($years > 0) {
                        $msg = "¡Mañana hacéis {$years} año(s) y {$remainingMonths} mes(es)! 🎉";
                    } else {
                        $msg = "¡Mañana hacéis {$months} mes(es)! 🎉";
                    }

                    if ($u1->fcm_token) $fcm->sendToToken($u1->fcm_token, "¡Fecha especial a la vista! ❤️", $msg);
                    if ($u2->fcm_token) $fcm->sendToToken($u2->fcm_token, "¡Fecha especial a la vista! ❤️", $msg);
                }
            }
        }
    }
})->dailyAt('19:00');

// MILESTONES REMINDER
Schedule::call(function () {
    $fcm = new FcmService();
    $tomorrow = now()->addDay()->startOfDay();
    
    $milestones = CoupleMilestone::whereDate('date', $tomorrow)->get();
    foreach ($milestones as $m) {
        $couple = Couple::find($m->couple_id);
        if ($couple) {
            $u1 = User::find($couple->user1_id);
            $u2 = User::find($couple->user2_id);
            if ($u1 && $u1->fcm_token) $fcm->sendToToken($u1->fcm_token, "¡Mañana es un día especial! 🎉", "Recordatorio: {$m->title}");
            if ($u2 && $u2->fcm_token) $fcm->sendToToken($u2->fcm_token, "¡Mañana es un día especial! 🎉", "Recordatorio: {$m->title}");
        }
    }
})->dailyAt('10:00');

// WEEKLY SUMMARY
Schedule::call(function () {
    $fcm = new FcmService();
    $couples = Couple::all();
    foreach ($couples as $couple) {
        $u1 = User::find($couple->user1_id);
        $u2 = User::find($couple->user2_id);
        if ($u1 && $u1->fcm_token) $fcm->sendToToken($u1->fcm_token, "Resumen semanal 📊", "¡Entra a la app para ver las novedades de esta semana con tu pareja!");
        if ($u2 && $u2->fcm_token) $fcm->sendToToken($u2->fcm_token, "Resumen semanal 📊", "¡Entra a la app para ver las novedades de esta semana con tu pareja!");
    }
})->weeklyOn(0, '10:00'); // Sundays at 10:00

