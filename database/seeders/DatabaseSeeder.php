<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CvTemplateSeeder::class,
            ProfileSectionSeeder::class,
            CompanySeeder::class,
            CvVersionSeeder::class,
            JobApplicationSeeder::class,
        ]);
    }
}
