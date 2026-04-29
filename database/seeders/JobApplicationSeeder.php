<?php

namespace Database\Seeders;

use App\Enums\ApplicationSource;
use App\Enums\ApplicationStatus;
use App\Models\Company;
use App\Models\JobApplication;
use App\Models\StatusHistory;
use Illuminate\Database\Seeder;

class JobApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();

        if ($companies->isEmpty()) {
            return;
        }

        $applications = [
            // Saved (2)
            [
                'company_index' => 0,
                'role_title' => 'Staff Software Engineer',
                'source' => ApplicationSource::CompanyWebsite,
                'status' => ApplicationStatus::Saved,
                'excitement_level' => 4,
                'days_ago' => 1,
            ],
            [
                'company_index' => 1,
                'role_title' => 'Senior Backend Engineer',
                'source' => ApplicationSource::JobBoard,
                'status' => ApplicationStatus::Saved,
                'excitement_level' => 3,
                'days_ago' => 2,
            ],

            // Applied (4)
            [
                'company_index' => 2,
                'role_title' => 'Full Stack Developer',
                'source' => ApplicationSource::Referral,
                'status' => ApplicationStatus::Applied,
                'excitement_level' => 5,
                'days_ago' => 5,
            ],
            [
                'company_index' => 3,
                'role_title' => 'Senior PHP Developer',
                'source' => ApplicationSource::Referral,
                'status' => ApplicationStatus::Applied,
                'excitement_level' => 4,
                'days_ago' => 7,
            ],
            [
                'company_index' => 4,
                'role_title' => 'Frontend Engineer',
                'source' => ApplicationSource::JobBoard,
                'status' => ApplicationStatus::Applied,
                'excitement_level' => 3,
                'days_ago' => 10,
            ],
            [
                'company_index' => 5,
                'role_title' => 'Platform Engineer',
                'source' => ApplicationSource::Recruiter,
                'status' => ApplicationStatus::Applied,
                'excitement_level' => 4,
                'days_ago' => 12,
            ],

            // Interviewing (4)
            [
                'company_index' => 6,
                'role_title' => 'Senior Software Engineer',
                'source' => ApplicationSource::Referral,
                'status' => ApplicationStatus::Interviewing,
                'excitement_level' => 5,
                'days_ago' => 15,
                'responded_days' => 10,
            ],
            [
                'company_index' => 7,
                'role_title' => 'Tech Lead',
                'source' => ApplicationSource::Referral,
                'status' => ApplicationStatus::Interviewing,
                'excitement_level' => 5,
                'days_ago' => 18,
                'responded_days' => 14,
            ],
            [
                'company_index' => 8,
                'role_title' => 'Laravel Developer',
                'source' => ApplicationSource::JobBoard,
                'status' => ApplicationStatus::Interviewing,
                'excitement_level' => 4,
                'days_ago' => 20,
                'responded_days' => 16,
            ],
            [
                'company_index' => 9,
                'role_title' => 'Engineering Manager',
                'source' => ApplicationSource::Referral,
                'status' => ApplicationStatus::Interviewing,
                'excitement_level' => 5,
                'days_ago' => 22,
                'responded_days' => 18,
            ],

            // Offer (2)
            [
                'company_index' => 0,
                'role_title' => 'Principal Engineer',
                'source' => ApplicationSource::Referral,
                'status' => ApplicationStatus::Offer,
                'excitement_level' => 5,
                'days_ago' => 30,
                'responded_days' => 20,
            ],
            [
                'company_index' => 2,
                'role_title' => 'Senior Developer',
                'source' => ApplicationSource::Referral,
                'status' => ApplicationStatus::Offer,
                'excitement_level' => 4,
                'days_ago' => 25,
                'responded_days' => 15,
            ],

            // Rejected (4)
            [
                'company_index' => 1,
                'role_title' => 'Backend Developer',
                'source' => ApplicationSource::JobBoard,
                'status' => ApplicationStatus::Rejected,
                'excitement_level' => 2,
                'days_ago' => 35,
                'responded_days' => 28,
            ],
            [
                'company_index' => 3,
                'role_title' => 'API Engineer',
                'source' => ApplicationSource::Recruiter,
                'status' => ApplicationStatus::Rejected,
                'excitement_level' => 3,
                'days_ago' => 40,
                'responded_days' => 30,
            ],
            [
                'company_index' => 5,
                'role_title' => 'DevOps Engineer',
                'source' => ApplicationSource::JobBoard,
                'status' => ApplicationStatus::Rejected,
                'excitement_level' => 2,
                'days_ago' => 45,
                'responded_days' => 35,
            ],
            [
                'company_index' => 7,
                'role_title' => 'Software Architect',
                'source' => ApplicationSource::CompanyWebsite,
                'status' => ApplicationStatus::Rejected,
                'excitement_level' => 3,
                'days_ago' => 50,
                'responded_days' => 40,
            ],

            // Closed (4)
            [
                'company_index' => 4,
                'role_title' => 'React Developer',
                'source' => ApplicationSource::JobBoard,
                'status' => ApplicationStatus::Closed,
                'excitement_level' => 2,
                'days_ago' => 55,
                'responded_days' => 45,
            ],
            [
                'company_index' => 6,
                'role_title' => 'UI Engineer',
                'source' => ApplicationSource::Referral,
                'status' => ApplicationStatus::Closed,
                'excitement_level' => 3,
                'days_ago' => 60,
                'responded_days' => 50,
            ],
            [
                'company_index' => 8,
                'role_title' => 'PHP Developer',
                'source' => ApplicationSource::Referral,
                'status' => ApplicationStatus::Closed,
                'excitement_level' => 2,
                'days_ago' => 65,
                'responded_days' => 55,
            ],
            [
                'company_index' => 9,
                'role_title' => 'Full Stack Engineer',
                'source' => ApplicationSource::JobBoard,
                'status' => ApplicationStatus::Closed,
                'excitement_level' => 3,
                'days_ago' => 70,
                'responded_days' => 60,
            ],
        ];

        foreach ($applications as $appData) {
            $company = $companies[$appData['company_index']];
            $appliedAt = now()->subDays($appData['days_ago']);
            $respondedAt = isset($appData['responded_days']) ? now()->subDays($appData['responded_days']) : null;

            $application = JobApplication::create([
                'company_id' => $company->id,
                'role_title' => $appData['role_title'],
                'source' => $appData['source'],
                'status' => $appData['status'],
                'excitement_level' => $appData['excitement_level'],
                'applied_at' => $appliedAt,
                'responded_at' => $respondedAt,
                'notes' => "Application for {$appData['role_title']} at {$company->name}.",
            ]);

            // // Create status history
            // StatusHistory::create([
            //     'job_application_id' => $application->id,
            //     'from_status' => null,
            //     'to_status' => ApplicationStatus::Saved->value,
            //     'changed_at' => $appliedAt,
            // ]);

            // if ($respondedAt) {
            //     StatusHistory::create([
            //         'job_application_id' => $application->id,
            //         'from_status' => ApplicationStatus::Saved->value,
            //         'to_status' => $appData['status']->value,
            //         'changed_at' => $respondedAt,
            //     ]);
            // }
        }
    }
}
