<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DesafioResource extends JsonResource
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
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'icono' => $this->icono,
            'conseguido_en' => $this->whenPivotLoaded('desafio_usuario', function () {
                return $this->pivot->conseguido_en;
            }),
        ];
    }
}
