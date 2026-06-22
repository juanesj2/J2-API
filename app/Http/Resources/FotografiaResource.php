<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FotografiaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Obtenemos el usuario autenticado
        $user = $request->user(); 

        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'direccion_imagen' => $this->direccion_imagen,
            'usuario_id' => $this->usuario_id,
            'vetada' => (bool)$this->vetada,
            
            // Relación con el usuario que subió la foto
            'user' => new UserResource($this->whenLoaded('user')),
            
            'likes_count' => $this->resource->relationLoaded('likes') ? $this->likes->count() : $this->likesCount(),
            
            // Comprobamos si el usuario autenticado ha dado like a esta foto
            'likedByUser' => $user ? $this->likes->contains('usuario_id', $user->id) : false,

            // Comprobamos si el usuario autenticado ha comentado en esta foto
            'comentadoPorUsuario' => $user ? $this->comentarios->contains('usuario_id', $user->id) : false,

            'comentarios_count' => $this->comentariosCount(), // Lo mismo aplica aquí
            
            'url' => str_starts_with($this->direccion_imagen, 'http') ? $this->direccion_imagen : url($this->direccion_imagen),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}