<?php

namespace Tests\Unit\Services;

use App\Services\IdGeneratorService;
use Tests\TestCase;

class IdGeneratorServiceTest extends TestCase
{
    public function test_generate_membership_number_returns_expected_format(): void
    {
        $service = new IdGeneratorService();
        $id = $service->generateMembershipNumber();

        $this->assertMatchesRegularExpression('/^MBR-\d{4}-\d{5}$/', $id);
    }
}
