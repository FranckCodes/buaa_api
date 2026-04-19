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
            CreditTypeSeeder::class,
            CreditStatusSeeder::class,
            OrderTypeSeeder::class,
            OrderStatusSeeder::class,
            ReportTypeSeeder::class,
            ReportStatusSeeder::class,
            SupportCategorySeeder::class,
            PostTagSeeder::class,
            PostStatusSeeder::class,
            InsuranceTypeSeeder::class,
            InsuranceStatusSeeder::class,
            SuperAdminSeeder::class,
        ]);
    }
}
