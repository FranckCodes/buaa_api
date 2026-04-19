<?php

namespace App\Services;

use Carbon\Carbon;

class IdGeneratorService
{
    public function generateClientId(): string
    {
        return 'CLT-' . str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT);
    }

    public function generateAdminId(): string
    {
        return 'ADM-' . str_pad((string) random_int(1, 999), 3, '0', STR_PAD_LEFT);
    }

    public function generateSupervisorId(): string
    {
        return 'SUP-' . str_pad((string) random_int(1, 999), 3, '0', STR_PAD_LEFT);
    }

    public function generateGenericUserId(): string
    {
        return 'USR-' . str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT);
    }

    public function generateCreditId(): string
    {
        return 'CRD-' . Carbon::now()->format('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 4));
    }

    public function generateBusinessPlanId(): string
    {
        return 'BP-' . Carbon::now()->format('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 4));
    }

    public function generateOrderId(): string
    {
        return 'CMD-' . now()->format('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 4));
    }

    public function generateReportId(): string
    {
        return 'RPT-' . now()->format('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 4));
    }

    public function generateMembershipNumber(): string
    {
        return 'MBR-' . Carbon::now()->format('Y') . '-' . str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT);
    }
}
