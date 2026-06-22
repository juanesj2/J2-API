<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use App\Models\Desafio;
use App\Models\Comentarios;
use App\Models\Fotografia;
use App\Models\Grupo;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
        'vetado',
        'telefono',
        'estado',
        'current_mood',
        'app',
        'pairing_code',
        'birth_date',
        'avatar_url',
    ];

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\CustomResetPassword($token));
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->pairing_code)) {
                $user->pairing_code = strtoupper(substr(uniqid(), -6));
            }
        });
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'vetado_hasta' => 'datetime',
        ];
    }

    // Relaciones
    public function fotografias() {
        return $this->hasMany(Fotografia::class, 'usuario_id');
    }

    public function desafios()
    {
        return $this->belongsToMany(Desafio::class, 'desafio_usuario', 'usuario_id', 'desafio_id')
                    ->withTimestamps()
                    ->withPivot('conseguido_en');
    }

    public function grupos()
    {
        return $this->belongsToMany(Grupo::class, 'grupo_usuarios', 'usuario_id', 'grupo_id')
                    ->withTimestamps()
                    ->withPivot('rol');
    }

    /* Esta funcion se encarga de dar el logro de Coleccionista */
    public function verificarColeccionista()
    {
        $coleccionista = Desafio::where('titulo', 'Coleccionista')->first();

        if ($this->desafios()->count() >= 5 && $coleccionista && !$this->desafios->contains($coleccionista->id)) {
            $this->desafios()->attach($coleccionista->id, ['conseguido_en' => now()]);
        }
    }

    public function asignarDesafio($titulo)
    {
        $desafio = Desafio::where('titulo', $titulo)->first();
        if ($desafio && !$this->hasDesafio($desafio->id)) {
            $this->desafios()->attach($desafio->id, ['conseguido_en' => now()]);
        }
    }

    public function verificarTodosLosDesafios()
    {
        // 1. Primer paso (1 foto)
        if ($this->fotografias()->count() >= 1) $this->asignarDesafio('Primer paso');
        
        // 2. Cinco capturas (5 fotos)
        if ($this->fotografias()->count() >= 5) $this->asignarDesafio('Cinco capturas');
        
        // 3. Me gusta esto (1 like recibido en total)
        $likesRecibidos = Fotografia::where('usuario_id', $this->id)->withCount('likes')->get()->sum('likes_count');
        if ($likesRecibidos >= 1) $this->asignarDesafio('Me gusta esto');
        
        // 4. Popular (25 likes en al menos una foto)
        $maxLikes = Fotografia::where('usuario_id', $this->id)->withCount('likes')->get()->max('likes_count');
        if ($maxLikes >= 25) $this->asignarDesafio('Popular');
        
        // 5. Social (10 comentarios hechos)
        $comentariosHechos = Comentarios::where('usuario_id', $this->id)->count();
        if ($comentariosHechos >= 10) $this->asignarDesafio('Social');
        
        // 6. Coleccionista (5 o más logros en total)
        $this->verificarColeccionista();
        
        // Refrescamos la relación para devolver todo actualizado
        $this->load('desafios');
    }

    /* Comprobamos si el usaurio tiene ya el desafio */
    public function hasDesafio($desafioId)
    {
        return $this->desafios->contains('id', $desafioId);
    }

    /**
     * Verifica si el usuario está vetado actualmente
     */
    public function estaVetado(): bool
    {
        return $this->vetado_hasta && now()->lt($this->vetado_hasta);
    }

    /**
     * Devuelve cuánto tiempo le queda de veto, o null si no está vetado
     */
    public function tiempoRestanteVeto(): ?string // Esto indica que puede devolver un string o null
    {
        if ($this->estaVetado()) {
            return now()->diffForHumans($this->vetado_hasta, [ // La funcion diffForHumans() devuelve un texto con el tiempo restante es una funcion de Carbon
                'parts' => 2, // Para mostrar solo días y horas
                'syntax' => Carbon::DIFF_RELATIVE_TO_NOW,
                'short' => true
            ]);
        }
        return null;
    }
}
