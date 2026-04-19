<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Reference\ReportStatus;
use App\Models\Reference\ReportType;
use App\Models\Report;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition(): array
    {
        return [
            'id'               => 'RPT-' . now()->format('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 4)),
            'client_id'        => Client::factory(),
            'superviseur_id'   => null,
            'report_type_id'   => ReportType::query()->first()?->id,
            'report_status_id' => ReportStatus::where('code', 'submitted')->first()?->id,
            'summary'          => fake()->sentence(),
            'value_numeric'    => fake()->randomFloat(2, 1, 1000),
            'value_unit'       => fake()->randomElement(['kg', 'USD', 't/ha']),
            'value_text'       => fake()->sentence(3),
            'details'          => fake()->paragraph(),
            'date_rapport'     => now()->toDateString(),
            'valide_par'       => null,
            'motif_rejet'      => null,
        ];
    }
}
