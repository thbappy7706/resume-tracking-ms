<?php

namespace Database\Seeders;

use App\Enums\CompanySize;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            [
                'name' => 'Google',
                'website' => 'https://google.com',
                'industry' => 'Technology',
                'size' => CompanySize::Large->value,
                'location' => 'Mountain View, CA',
                'notes' => 'FAANG company, highly competitive.',
            ],
            [
                'name' => 'Stripe',
                'website' => 'https://stripe.com',
                'industry' => 'Fintech',
                'size' => CompanySize::Large->value,
                'location' => 'San Francisco, CA',
                'notes' => 'Payment processing platform.',
            ],
            [
                'name' => 'Vercel',
                'website' => 'https://vercel.com',
                'industry' => 'Technology',
                'size' => CompanySize::Medium->value,
                'location' => 'Remote',
                'notes' => 'Frontend cloud platform, creators of Next.js.',
            ],
            [
                'name' => 'Shopify',
                'website' => 'https://shopify.com',
                'industry' => 'E-commerce',
                'size' => CompanySize::Large->value,
                'location' => 'Ottawa, Canada',
                'notes' => 'E-commerce platform for businesses of all sizes.',
            ],
            [
                'name' => 'Linear',
                'website' => 'https://linear.app',
                'industry' => 'Technology',
                'size' => CompanySize::Small->value,
                'location' => 'Remote',
                'notes' => 'Project management tool, known for excellent UX.',
            ],
            [
                'name' => 'Notion',
                'website' => 'https://notion.so',
                'industry' => 'Technology',
                'size' => CompanySize::Medium->value,
                'location' => 'San Francisco, CA',
                'notes' => 'All-in-one workspace.',
            ],
            [
                'name' => 'Figma',
                'website' => 'https://figma.com',
                'industry' => 'Design',
                'size' => CompanySize::Medium->value,
                'location' => 'San Francisco, CA',
                'notes' => 'Collaborative design tool.',
            ],
            [
                'name' => 'GitLab',
                'website' => 'https://gitlab.com',
                'industry' => 'Technology',
                'size' => CompanySize::Large->value,
                'location' => 'Remote',
                'notes' => 'All-remote company, DevOps platform.',
            ],
            [
                'name' => 'Laravel',
                'website' => 'https://laravel.com',
                'industry' => 'Technology',
                'size' => CompanySize::Small->value,
                'location' => 'Remote',
                'notes' => 'PHP framework company.',
            ],
            [
                'name' => 'Atlassian',
                'website' => 'https://atlassian.com',
                'industry' => 'Technology',
                'size' => CompanySize::Large->value,
                'location' => 'Sydney, Australia',
                'notes' => 'Jira, Confluence, Bitbucket.',
            ],
        ];

        foreach ($companies as $company) {
            Company::firstOrCreate(
                ['name' => $company['name']],
                array_merge($company, ['slug' => Str::slug($company['name'])])
            );
        }
    }
}
