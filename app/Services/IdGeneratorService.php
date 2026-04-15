<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;

class IdGeneratorService
{
    public function generateMembershipNumber(): string
    {
        return 'MBR-' . Carbon::now()->format('Y') . '-' . str_pad((string) random_int(1, 99999), 5, '0', STR_PAD_LEFT);
    }
}
