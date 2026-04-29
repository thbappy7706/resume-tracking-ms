<?php

namespace Database\Seeders;

use App\Models\CvTemplate;
use App\Models\CvVersion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CvVersionSeeder extends Seeder
{
    public function run(): void
    {
        $templates = CvTemplate::all();

        if ($templates->isEmpty()) {
            return;
        }

        $versions = [
            [
                'template_index' => 0,
                'name' => 'Senior Developer CV',
                'target_role' => 'Senior Software Engineer',
                'target_industry' => 'Technology',
                'notes' => 'Primary CV for senior engineering roles.',
                'is_base' => true,
            ],
            [
                'template_index' => 1,
                'name' => 'Tech Lead CV',
                'target_role' => 'Tech Lead / Engineering Manager',
                'target_industry' => 'Technology',
                'notes' => 'Focused on leadership and team management experience.',
                'is_base' => false,
            ],
            [
                'template_index' => 2,
                'name' => 'Freelance CV',
                'target_role' => 'Full Stack Developer',
                'target_industry' => 'E-commerce',
                'notes' => 'Highlighting freelance and project-based work.',
                'is_base' => false,
            ],
        ];

        foreach ($versions as $version) {
            $template = $templates[$version['template_index']];

            CvVersion::firstOrCreate(
                ['name' => $version['name']],
                [
                    'cv_template_id' => $template->id,
                    'slug' => Str::slug($version['name']).'-'.Str::ulid(),
                    'target_role' => $version['target_role'],
                    'target_industry' => $version['target_industry'],
                    'notes' => $version['notes'],
                    'is_base' => $version['is_base'],
                ]
            );
        }
    }
}
