<?php

namespace App\Services;

use App\Models\Couple;
use App\Models\User;

class CouplePairingService
{
    public function getCoupleForUser($userId)
    {
        return Couple::where('user1_id', $userId)
            ->orWhere('user2_id', $userId)
            ->first();
    }

    public function pair(User $user, string $pairingCode)
    {
        if ($this->getCoupleForUser($user->id)) {
            throw new \Exception('Ya estás vinculado a una pareja.', 400);
        }

        if (strtoupper($pairingCode) === strtoupper($user->pairing_code)) {
            throw new \Exception('No puedes vincularte contigo mismo.', 400);
        }

        $partner = User::where('pairing_code', strtoupper($pairingCode))->first();

        if (!$partner) {
            throw new \Exception('Código de vinculación inválido.', 404);
        }

        if ($this->getCoupleForUser($partner->id)) {
            throw new \Exception('Ese usuario ya está vinculado a otra persona.', 400);
        }

        return Couple::create([
            'user1_id' => $user->id,
            'user2_id' => $partner->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function unpair(User $user)
    {
        $couple = $this->getCoupleForUser($user->id);

        if (!$couple) {
            throw new \Exception('No estás vinculado a ninguna pareja.', 403);
        }

        $couple->delete();
        return true;
    }
}
