<?php

namespace Database\Factories;

use App\Enum\ReportReason;
use App\Enum\ReportStatus;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->uuid(),
            'reason' => collect(array_column(ReportReason::cases(), 'value'))->random(),
            'status' => collect(array_column(ReportStatus::cases(), 'value'))->random(),
            'reportable_id' => Genre::factory()->create()->id,
            'reportable_type' => "App\\Models\\Genre",
            'user_id' => User::factory()->create()->id
        ];
    }
}
