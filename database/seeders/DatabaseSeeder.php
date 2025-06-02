<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Report;
use App\Models\ReportMessage;
use App\Models\Partida;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // <-- aqui

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(4)->create();
        Report::factory(4)->create();
        ReportMessage::factory(10)->create();
        Partida::factory(4)->create();
    \App\Models\User::create([
        'name' => 'ADMIN',
        'email' => 'blackjacktcc@gmail.com',
        'email_verified_at' => now(),
        'password' => Hash::make('senha1'),
        'role' => 'admin',
    ]);
    }
}
