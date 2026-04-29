<?php

use App\Enums\ApplicationStatus;
use App\Enums\CvLayout;
use App\Enums\SectionType;
use App\Models\Company;
use App\Models\CvSectionOverride;
use App\Models\CvTemplate;
use App\Models\CvVersion;
use App\Models\JobApplication;
use App\Models\ProfileSection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('resolves cv version sections with override data', function () {
    $template = CvTemplate::create([
        'name' => 'Default CV',
        'slug' => 'default-cv',
        'description' => 'A default CV template.',
        'layout' => CvLayout::Classic,
        'config' => ['font_family' => 'Inter', 'accent_color' => '#111', 'section_order' => ['experience'], 'spacing' => 'normal', 'show_photo' => true],
    ]);

    $version = CvVersion::create([
        'cv_template_id' => $template->id,
        'name' => 'Software Engineer',
        'slug' => 'software-engineer',
        'target_role' => 'Software Engineer',
        'is_base' => true,
    ]);

    $section = ProfileSection::create([
        'type' => SectionType::Experience,
        'title' => 'Previous role',
        'organization' => 'Acme Corp',
        'sort_order' => 1,
        'description' => 'Built products.',
    ]);

    CvSectionOverride::create([
        'cv_version_id' => $version->id,
        'profile_section_id' => $section->id,
        'is_included' => true,
        'sort_order' => 1,
        'override_title' => 'Override title',
        'override_description' => 'Override description',
    ]);

    $resolved = $version->resolvedSections();

    expect($resolved)->toHaveCount(1);
    expect($resolved->first()->title)->toBe('Override title');
    expect($resolved->first()->description)->toBe('Override description');
});

it('computes salary range display and overdue flag for job applications', function () {
    $template = CvTemplate::create([
        'name' => 'Default CV',
        'slug' => 'default-cv-2',
        'description' => 'A default CV template.',
        'layout' => CvLayout::Classic,
        'config' => ['font_family' => 'Inter', 'accent_color' => '#111', 'section_order' => ['experience'], 'spacing' => 'normal', 'show_photo' => true],
    ]);

    $version = CvVersion::create([
        'cv_template_id' => $template->id,
        'name' => 'Data Analyst',
        'slug' => 'data-analyst',
        'is_base' => true,
    ]);

    $company = Company::create([
        'name' => 'Acme Corp',
        'slug' => 'acme-corp',
    ]);

    $application = JobApplication::create([
        'company_id' => $company->id,
        'cv_version_id' => $version->id,
        'role_title' => 'Data Analyst',
        'source' => 'job_board',
        'salary_min' => 80000,
        'salary_max' => 95000,
        'deadline' => now()->subDays(1)->toDateString(),
        'status' => ApplicationStatus::Applied,
    ]);

    expect($application->salary_range_display)->toBe('USD 80000–95000');
    expect($application->is_overdue)->toBeTrue();
});
