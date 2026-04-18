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
            'client_id'        => Client::factory(),
            'superviseur_id'   => null,
            'report_type_id'   => ReportType::first()?->id,
            'report_status_id' => ReportStatus::where('code', 'submitted')->first()?->id,
            'summary'          => fake()->sentence(),
            'value_numeric'    => fake()->randomFloat(2, 0, 1000),
            'value_unit'       => 'kg',
            'details'          => fake()->paragraph(),
            'date_rapport'     => now()->toDateString(),
        ];
    }
}
