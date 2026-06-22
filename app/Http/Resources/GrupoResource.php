<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GrupoResource extends JsonResource
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
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'codigo_invitacion' => $this->codigo_invitacion,
            'creado_por' => $this->creado_por,
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'usuarios' => UserResource::collection($this->whenLoaded('usuarios')),
        ];
    }
}
