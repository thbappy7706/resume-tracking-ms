<?php

namespace Database\Seeders;

use App\Enums\SectionType;
use App\Models\ProfileSection;
use Illuminate\Database\Seeder;

class ProfileSectionSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            // Summary (2)
            [
                'type' => SectionType::Summary,
                'title' => 'Professional Summary',
                'description' => 'Experienced software engineer with 8+ years building scalable web applications. Passionate about clean code, system design, and mentoring junior developers.',
                'sort_order' => 1,
            ],
            [
                'type' => SectionType::Summary,
                'title' => 'Career Objective',
                'description' => 'Seeking a senior engineering role where I can leverage my expertise in distributed systems and team leadership.',
                'sort_order' => 2,
            ],

            // Experience (5)
            [
                'type' => SectionType::Experience,
                'title' => 'Senior Software Engineer',
                'organization' => 'TechCorp Inc.',
                'location' => 'San Francisco, CA',
                'start_date' => '2021-03-01',
                'is_current' => true,
                'description' => "• Led a team of 6 engineers building a microservices platform serving 2M+ daily requests\n• Architected event-driven system using Kafka, reducing latency by 40%\n• Implemented CI/CD pipelines with GitHub Actions, cutting deployment time from 2 hours to 15 minutes\n• Mentored 3 junior developers through structured code review and pair programming",
                'sort_order' => 10,
            ],
            [
                'type' => SectionType::Experience,
                'title' => 'Software Engineer',
                'organization' => 'StartupXYZ',
                'location' => 'Remote',
                'start_date' => '2019-01-01',
                'end_date' => '2021-02-28',
                'description' => "• Built and maintained React-based dashboard used by 500+ enterprise clients\n• Developed RESTful APIs with Laravel serving 100K+ requests per day\n• Reduced page load time by 60% through code splitting and lazy loading\n• Collaborated with product team to define and ship 12 major features",
                'sort_order' => 11,
            ],
            [
                'type' => SectionType::Experience,
                'title' => 'Junior Developer',
                'organization' => 'WebAgency',
                'location' => 'New York, NY',
                'start_date' => '2017-06-01',
                'end_date' => '2018-12-31',
                'description' => "• Developed responsive websites for 20+ clients using modern CSS frameworks\n• Built custom WordPress plugins and themes\n• Participated in agile sprints and daily standups",
                'sort_order' => 12,
            ],
            [
                'type' => SectionType::Experience,
                'title' => 'Software Engineering Intern',
                'organization' => 'BigTech Corp',
                'location' => 'Seattle, WA',
                'start_date' => '2016-06-01',
                'end_date' => '2016-09-30',
                'description' => "• Built internal tooling for monitoring service health\n• Wrote unit and integration tests achieving 90% code coverage\n• Presented project demo to engineering leadership",
                'sort_order' => 13,
            ],
            [
                'type' => SectionType::Experience,
                'title' => 'Freelance Developer',
                'organization' => 'Self-Employed',
                'location' => 'Remote',
                'start_date' => '2015-01-01',
                'end_date' => '2017-05-31',
                'description' => "• Delivered 15+ web projects for small businesses\n• Specialized in e-commerce solutions with Stripe integration\n• Managed client relationships and project timelines",
                'sort_order' => 14,
            ],

            // Education (3)
            [
                'type' => SectionType::Education,
                'title' => 'Master of Science in Computer Science',
                'organization' => 'Stanford University',
                'location' => 'Stanford, CA',
                'start_date' => '2015-09-01',
                'end_date' => '2017-06-30',
                'description' => 'Focus on Distributed Systems and Machine Learning. GPA: 3.9/4.0',
                'sort_order' => 20,
            ],
            [
                'type' => SectionType::Education,
                'title' => 'Bachelor of Science in Computer Science',
                'organization' => 'UC Berkeley',
                'location' => 'Berkeley, CA',
                'start_date' => '2011-09-01',
                'end_date' => '2015-05-30',
                'description' => 'Dean\'s List all semesters. Relevant coursework: Data Structures, Algorithms, Operating Systems, Database Systems.',
                'sort_order' => 21,
            ],
            [
                'type' => SectionType::Education,
                'title' => 'AWS Certified Solutions Architect',
                'organization' => 'Amazon Web Services',
                'start_date' => '2022-01-01',
                'end_date' => '2025-01-01',
                'description' => 'Professional-level certification for designing distributed systems on AWS.',
                'sort_order' => 22,
            ],

            // Skills (10)
            [
                'type' => SectionType::Skill,
                'title' => 'PHP / Laravel',
                'meta' => ['skills' => ['PHP', 'Laravel', 'Livewire', 'Eloquent ORM']],
                'sort_order' => 30,
            ],
            [
                'type' => SectionType::Skill,
                'title' => 'JavaScript / TypeScript',
                'meta' => ['skills' => ['JavaScript', 'TypeScript', 'Node.js', 'React', 'Vue.js']],
                'sort_order' => 31,
            ],
            [
                'type' => SectionType::Skill,
                'title' => 'Databases',
                'meta' => ['skills' => ['PostgreSQL', 'MySQL', 'Redis', 'MongoDB']],
                'sort_order' => 32,
            ],
            [
                'type' => SectionType::Skill,
                'title' => 'Cloud & DevOps',
                'meta' => ['skills' => ['AWS', 'Docker', 'Kubernetes', 'GitHub Actions', 'Terraform']],
                'sort_order' => 33,
            ],
            [
                'type' => SectionType::Skill,
                'title' => 'Testing',
                'meta' => ['skills' => ['PHPUnit', 'Pest', 'Jest', 'Cypress', 'Laravel Dusk']],
                'sort_order' => 34,
            ],
            [
                'type' => SectionType::Skill,
                'title' => 'Architecture',
                'meta' => ['skills' => ['Microservices', 'Event Sourcing', 'CQRS', 'REST APIs', 'GraphQL']],
                'sort_order' => 35,
            ],
            [
                'type' => SectionType::Skill,
                'title' => 'Frontend',
                'meta' => ['skills' => ['Tailwind CSS', 'Shadcn UI', 'Inertia.js', 'Next.js', 'Vite']],
                'sort_order' => 36,
            ],
            [
                'type' => SectionType::Skill,
                'title' => 'Tools',
                'meta' => ['skills' => ['Git', 'VS Code', 'Postman', 'Figma', 'Jira']],
                'sort_order' => 37,
            ],
            [
                'type' => SectionType::Skill,
                'title' => 'Methodologies',
                'meta' => ['skills' => ['Agile/Scrum', 'TDD', 'CI/CD', 'Code Review', 'Pair Programming']],
                'sort_order' => 38,
            ],
            [
                'type' => SectionType::Skill,
                'title' => 'Soft Skills',
                'meta' => ['skills' => ['Team Leadership', 'Mentoring', 'Technical Writing', 'Public Speaking']],
                'sort_order' => 39,
            ],
        ];

        $sortOrder = 1;
        foreach ($sections as $section) {
            ProfileSection::firstOrCreate(
                ['type' => $section['type'], 'title' => $section['title']],
                array_merge($section, ['sort_order' => $sortOrder++])
            );
        }
    }
}
