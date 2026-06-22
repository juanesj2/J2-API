<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name' => 'pepe',
                'email' => 'pepe@gmail.com',
                'password' => Hash::make('pepe666666'),
                'rol' => 'admin',
                'vetado_hasta' => null,

            ],
            [
                'name' => 'elias',
                'email' => 'elias@gmail.com',
                'password' => Hash::make('elias666666'),
                'rol' => 'usuario',
                'vetado_hasta' => null,
            ],
            [
                'name' => 'dario',
                'email' => 'dario@gmail.com',
                'password' => Hash::make('dario666666'),
                'rol' => 'usuario',
                'vetado_hasta' => null,
            ]
        ];

        foreach ($users as $user) {
            User::Create($user);
        }
    }
}
