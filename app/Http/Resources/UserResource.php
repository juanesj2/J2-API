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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at,
            'rol' => $this->rol,
            'updated_at' => $this->updated_at,
            'pairing_code' => $this->pairing_code,
            'esta_vetado' => $this->estaVetado(),
            'tiempo_restante_veto' => $this->tiempoRestanteVeto(),
            'fotografias' => FotografiaResource::collection($this->whenLoaded('fotografias')),
            'desafios' => DesafioResource::collection($this->whenLoaded('desafios')),
        ];
    }
}
