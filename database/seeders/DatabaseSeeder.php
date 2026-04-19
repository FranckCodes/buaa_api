<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserStatusSeeder::class,
            ClientActivityTypeSeeder::class,
            ClientStructureTypeSeeder::class,
            AdhesionTypeSeeder::class,
            AdhesionStatusSeeder::class,
            CreditTypeSeeder::class,
            CreditStatusSeeder::class,
            InsuranceTypeSeeder::class,
            InsuranceStatusSeeder::class,
            OrderTypeSeeder::class,
            OrderStatusSeeder::class,
            ReportTypeSeeder::class,
            ReportStatusSeeder::class,
            PostTagSeeder::class,
            PostStatusSeeder::class,
            SupportCategorySeeder::class,
            PaymentModeSeeder::class,
            SuperAdminSeeder::class,
        ]);
    }
}
