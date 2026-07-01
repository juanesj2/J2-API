<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $couple = \App\Models\Couple::where('user1_id', $this->id)
            ->orWhere('user2_id', $this->id)
            ->first();
        
        $is_premium = $couple && $couple->premium_until && $couple->premium_until->isFuture();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at,
            'rol' => $this->rol,
            'updated_at' => $this->updated_at,
            'pairing_code' => $this->pairing_code,
            'is_premium' => $is_premium,
            'premium_until' => $couple ? $couple->premium_until : null,
            'esta_vetado' => $this->estaVetado(),
            'tiempo_restante_veto' => $this->tiempoRestanteVeto(),
            'fotografias' => FotografiaResource::collection($this->whenLoaded('fotografias')),
            'desafios' => DesafioResource::collection($this->whenLoaded('desafios')),
        ];
    }
}
