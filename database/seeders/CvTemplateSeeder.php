<?php

namespace Database\Seeders;

use App\Enums\CvLayout;
use App\Models\CvTemplate;
use Illuminate\Database\Seeder;

class CvTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Single Column Clean',
                'slug' => 'single-column-clean',
                'description' => 'A clean, minimalist single-column layout perfect for traditional industries.',
                'layout' => CvLayout::Classic,
                'config' => [
                    'font' => 'Inter',
                    'accent_color' => '#2563eb',
                    'show_photo' => false,
                ],
                'is_default' => true,
            ],
            [
                'name' => 'Two Column Modern',
                'slug' => 'two-column-modern',
                'description' => 'A modern two-column layout with sidebar for skills and contact info.',
                'layout' => CvLayout::Modern,
                'config' => [
                    'font' => 'Inter',
                    'accent_color' => '#059669',
                    'sidebar_width' => '30%',
                    'show_photo' => true,
                ],
                'is_default' => false,
            ],
            [
                'name' => 'Sidebar Minimal',
                'slug' => 'sidebar-minimal',
                'description' => 'A minimal sidebar layout with clean typography and subtle accents.',
                'layout' => CvLayout::Minimal,
                'config' => [
                    'font' => 'Inter',
                    'accent_color' => '#7c3aed',
                    'sidebar_width' => '25%',
                    'show_photo' => true,
                ],
                'is_default' => false,
            ],
        ];

        foreach ($templates as $template) {
            CvTemplate::firstOrCreate(
                ['slug' => $template['slug']],
                $template
            );
        }
    }
}
