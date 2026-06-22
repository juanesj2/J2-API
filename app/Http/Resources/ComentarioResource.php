<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComentarioResource extends JsonResource
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
            'contenido' => $this->contenido,
            'usuario_id' => $this->usuario_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'fotografia_id' => $this->fotografia_id,
            'created_at' => $this->created_at,
        ];
    }
}
