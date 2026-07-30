<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use App\DTOs\CoupleInventory;

class InventoryCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $data = $value ? json_decode($value, true) : [];
        if (!is_array($data)) {
            $data = [];
        }
        return new CoupleInventory($data);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (!$value instanceof CoupleInventory) {
            if (is_array($value)) {
                $value = new CoupleInventory($value);
            } else {
                $value = new CoupleInventory();
            }
        }
        
        $data = $value->toArray();
        // Remove virtual properties before saving to database
        unset($data['received_gifts']);
        unset($data['sent_gifts']);
        
        return json_encode($data);
    }
}
