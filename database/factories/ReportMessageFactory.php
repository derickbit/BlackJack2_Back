<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Report;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReportMessage>
 */
class ReportMessageFactory extends Factory
{
public function definition(): array
{
    // Pegue todos os reports com seus respectivos user_id
    $report = \App\Models\Report::inRandomOrder()->first();

    return [
        'report_id' => $report->id,
        'user_id' => $report->user_id, // Garante que o user é o dono do report
        'mensagem' => $this->faker->sentence(),
        'imagem' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ];
}
}
