<?php

namespace App\DTOs;

class CoupleInventory
{
    public int $gift_teddy;
    public int $gift_rose;
    public int $gift_ring;
    public array $letters;
    public bool $spicy_pack;
    public ?array $pet;
    public int $coins;
    public array $unlocked_pets;
    
    // Virtual properties populated on-the-fly
    public array $received_gifts;
    public array $sent_gifts;

    public function __construct(array $data = [])
    {
        $legacyGifts = 0;
        if (isset($data['gifts'])) {
            $legacyGifts = is_numeric($data['gifts']) ? (int)$data['gifts'] : ($data['gifts'] ? 1 : 0);
        }

        $this->gift_teddy = (int)($data['gift_teddy'] ?? 0) + $legacyGifts;
        $this->gift_rose = (int)($data['gift_rose'] ?? 0);
        $this->gift_ring = (int)($data['gift_ring'] ?? 0);
        
        $this->letters = is_array($data['letters'] ?? null) ? $data['letters'] : [];
        $this->spicy_pack = (bool)($data['spicy_pack'] ?? false);
        $this->pet = is_array($data['pet'] ?? null) ? $data['pet'] : null;
        $this->coins = (int)($data['coins'] ?? 0); // Give 0 starting coins
        $this->unlocked_pets = is_array($data['unlocked_pets'] ?? null) ? $data['unlocked_pets'] : [];
        
        $this->received_gifts = is_array($data['received_gifts'] ?? null) ? $data['received_gifts'] : [];
        $this->sent_gifts = is_array($data['sent_gifts'] ?? null) ? $data['sent_gifts'] : [];
    }

    public function toArray(): array
    {
        return [
            'gift_teddy' => $this->gift_teddy,
            'gift_rose' => $this->gift_rose,
            'gift_ring' => $this->gift_ring,
            'letters' => $this->letters,
            'spicy_pack' => $this->spicy_pack,
            'pet' => $this->pet,
            'coins' => $this->coins,
            'unlocked_pets' => $this->unlocked_pets,
            'received_gifts' => $this->received_gifts,
            'sent_gifts' => $this->sent_gifts,
        ];
    }
}
