<?php

namespace App\DTOs;

use ArrayAccess;
use JsonSerializable;

class CoupleInventory implements ArrayAccess, JsonSerializable
{
    public int $gift_teddy;
    public int $gift_rose;
    public int $gift_ring;
    public array $letters;
    public bool $spicy_pack;
    public ?array $pet;
    public int $coins;
    public int $eggs;
    public array $unlocked_pets;
    public array $owned_decorations;
    public ?string $daily_coin_date;
    
    // Virtual properties populated on-the-fly
    public array $received_gifts;
    public array $sent_gifts;

    protected array $dynamicAttributes = [];

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
        $this->eggs = (int)($data['eggs'] ?? 0);
        $this->unlocked_pets = is_array($data['unlocked_pets'] ?? null) ? $data['unlocked_pets'] : [];
        $this->owned_decorations = is_array($data['owned_decorations'] ?? null) ? $data['owned_decorations'] : [];
        $this->daily_coin_date = $data['daily_coin_date'] ?? null;
        
        $this->received_gifts = is_array($data['received_gifts'] ?? null) ? $data['received_gifts'] : [];
        $this->sent_gifts = is_array($data['sent_gifts'] ?? null) ? $data['sent_gifts'] : [];

        // Guardar cualquier otro campo adicional
        foreach ($data as $k => $v) {
            if (!property_exists($this, $k)) {
                $this->dynamicAttributes[$k] = $v;
            }
        }
    }

    public function offsetExists(mixed $offset): bool
    {
        return property_exists($this, $offset) || array_key_exists($offset, $this->dynamicAttributes);
    }

    public function &offsetGet(mixed $offset): mixed
    {
        if (property_exists($this, $offset)) {
            return $this->$offset;
        }
        if (array_key_exists($offset, $this->dynamicAttributes)) {
            return $this->dynamicAttributes[$offset];
        }
        $null = null;
        return $null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_null($offset)) {
            $this->dynamicAttributes[] = $value;
        } elseif (property_exists($this, $offset)) {
            $this->$offset = $value;
        } else {
            $this->dynamicAttributes[$offset] = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        if (property_exists($this, $offset)) {
            unset($this->$offset);
        } else {
            unset($this->dynamicAttributes[$offset]);
        }
    }

    public function __get(string $name): mixed
    {
        return $this->dynamicAttributes[$name] ?? null;
    }

    public function __set(string $name, mixed $value): void
    {
        $this->dynamicAttributes[$name] = $value;
    }

    public function __isset(string $name): bool
    {
        return property_exists($this, $name) || array_key_exists($name, $this->dynamicAttributes);
    }

    public function toArray(): array
    {
        $base = [
            'gift_teddy' => $this->gift_teddy,
            'gift_rose' => $this->gift_rose,
            'gift_ring' => $this->gift_ring,
            'letters' => $this->letters,
            'spicy_pack' => $this->spicy_pack,
            'pet' => $this->pet,
            'coins' => $this->coins,
            'eggs' => $this->eggs,
            'unlocked_pets' => $this->unlocked_pets,
            'owned_decorations' => $this->owned_decorations,
            'daily_coin_date' => $this->daily_coin_date,
            'received_gifts' => $this->received_gifts,
            'sent_gifts' => $this->sent_gifts,
        ];

        return array_merge($base, $this->dynamicAttributes);
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
