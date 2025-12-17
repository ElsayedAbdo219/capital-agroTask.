<?php

namespace Modules\User\Database\Seeders;

use Modules\User\Models\User;
use Illuminate\Database\Seeder;
use Modules\User\Enums\UserType;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
          'name' => 'ElsayedAbdo',
          'email' => 'ss@gmail.com',
          'password' => Hash::make('123123123'),
          'type' => UserType::ADMIN,
        ]);
    }
}
