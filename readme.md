<div align="center">

# 📄 CV Manager

**A personal full-stack application to manage CV versions, track job applications, and analyse your hiring funnel.**

Built with **Laravel 13 · React 19 · Inertia.js v2 · Glassmorphism UI**

[![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![React](https://img.shields.io/badge/React-19-61DAFB?style=flat-square&logo=react&logoColor=black)](https://react.dev)
[![Inertia](https://img.shields.io/badge/Inertia.js-v2-9553E9?style=flat-square)](https://inertiajs.com)
[![TypeScript](https://img.shields.io/badge/TypeScript-5-3178C6?style=flat-square&logo=typescript&logoColor=white)](https://www.typescriptlang.org)
[![Tailwind](https://img.shields.io/badge/Tailwind-v4-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/license-MIT-green?style=flat-square)](LICENSE)

</div>

---

## Table of Contents

- [Overview](#overview)
- [System Architecture](#system-architecture)
- [Feature Map](#feature-map)
- [Tech Stack](#tech-stack)
- [Database Schema](#database-schema)
- [Installation — With Laravel Sail (Docker)](#installation--with-laravel-sail-docker)
- [Installation — Without Sail (Local)](#installation--without-sail-local)
- [Environment Variables](#environment-variables)
- [Running the Application](#running-the-application)
- [Seeded Credentials](#seeded-credentials)
- [Key Laravel 13 Features Used](#key-laravel-13-features-used)
- [Project Structure](#project-structure)
- [Analytics Datasets](#analytics-datasets)
- [CV Template System](#cv-template-system)
- [Queue & Scheduler](#queue--scheduler)
- [Testing](#testing)
- [Troubleshooting](#troubleshooting)

---

## Overview

CV Manager solves a common problem: when applying for multiple jobs, you need tailored CVs for each role, but maintaining consistency with your base profile becomes painful. This app gives you:

- **One master profile** — fill it once. All your experience, skills, education, certifications.
- **Unlimited CV versions** — each pulls from your profile but lets you override any section, reorder content, and apply a different template.
- **Job application tracker** — link each application to the exact CV version you submitted. Track status through the full hiring funnel.
- **Analytics dashboard** — response rates, funnel visualisation, CV performance comparison, interview outcomes, weekly activity heatmap.

---

## System Architecture

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                              CV MANAGER SYSTEM                              │
└─────────────────────────────────────────────────────────────────────────────┘

                             ┌──────────────┐
                             │   Browser    │
                             │  React 19    │
                             │ + Inertia v2 │
                             └──────┬───────┘
                                    │ HTTP / Inertia XHR
                     ┌──────────────▼──────────────────┐
                     │          Laravel 13              │
                     │                                  │
                     │  ┌──────────┐  ┌─────────────┐  │
                     │  │  Routes  │  │  Middleware  │  │
                     │  │ web.php  │  │  (Auth, Rate │  │
                     │  └────┬─────┘  │   Limiter)  │  │
                     │       │        └─────────────┘  │
                     │  ┌────▼──────────────────────┐  │
                     │  │       Controllers          │  │
                     │  │  Dashboard │ Profile       │  │
                     │  │  CvVersion │ Application   │  │
                     │  │  Analytics │ Company       │  │
                     │  └────┬──────────────────────┘  │
                     │       │                          │
                     │  ┌────▼──────────────────────┐  │
                     │  │    Actions / Services      │  │
                     │  │  CreateJobApplication      │  │
                     │  │  UpdateApplicationStatus   │  │
                     │  │  ExportCvToPdf             │  │
                     │  │  AnalyticsService          │  │
                     │  └────┬──────────────────────┘  │
                     │       │                          │
                     │  ┌────▼──────────────────────┐  │
                     │  │   Laravel Pipeline         │  │
                     │  │  ValidateStatusTransition  │  │
                     │  │  CreateStatusHistory       │  │
                     │  │  UpdateTimestamps          │  │
                     │  │  NotifyStatusChange        │  │
                     │  └────┬──────────────────────┘  │
                     │       │                          │
                     └───────┼──────────────────────────┘
                             │
           ┌─────────────────┼──────────────────────┐
           │                 │                       │
    ┌──────▼──────┐   ┌─────▼──────┐   ┌───────────▼──────┐
    │  PostgreSQL  │   │   Queue    │   │  File Storage    │
    │  (Primary)   │   │ (Database) │   │  (PDF exports)   │
    │              │   │            │   │                  │
    │  11 Tables   │   │  Jobs:     │   │ storage/app/     │
    │  BIGINT PKs  │   │  PDF Gen   │   │ exports/*.pdf    │
    │  Soft deletes│   │  Reminders │   │                  │
    └─────────────┘   └────────────┘   └──────────────────┘

──────────────────────────────────────────────────────────────
                    DATA FLOW DIAGRAM
──────────────────────────────────────────────────────────────

  ┌──────────────────────────────────────────────────────────┐
  │  PROFILE STORE (Master Data)                             │
  │  profile_sections table                                  │
  │  Bio · Experience · Education · Skills · Certs · Links   │
  └──────────────────────┬───────────────────────────────────┘
                         │  resolvedSections()
                         │  (merges master + overrides)
  ┌──────────────────────▼───────────────────────────────────┐
  │  CV BUILDER                                              │
  │  cv_versions + cv_section_overrides + cv_templates       │
  │  Select sections → override content → pick template      │
  │  → Export PDF via Browsershot (queued job)               │
  └──────────────────────┬───────────────────────────────────┘
                         │  cv_version_id FK
  ┌──────────────────────▼───────────────────────────────────┐
  │  JOB TRACKER                                             │
  │  job_applications + companies + interviews               │
  │  Status: Saved → Applied → Screening → Interviewing      │
  │        → Offer → Accepted / Rejected / Withdrawn         │
  │  Every status change logged to status_histories          │
  └──────────────────────┬───────────────────────────────────┘
                         │  aggregated queries
                         │  Concurrency::run() parallel fetch
  ┌──────────────────────▼───────────────────────────────────┐
  │  ANALYTICS ENGINE                                        │
  │  AnalyticsService — 10 datasets                         │
  │  Funnel · Timeline · Source breakdown · CV performance   │
  │  Interview outcomes · Heatmap · Response rate           │
  │  Cached with Cache::tags(['analytics'])                  │
  └──────────────────────────────────────────────────────────┘

──────────────────────────────────────────────────────────────
                  STATUS TRANSITION MAP
──────────────────────────────────────────────────────────────

  [SAVED] ──────────────────────────────────────► [WITHDRAWN]
     │                                                  ▲
     ▼                                                  │
  [APPLIED] ──────────────────────────────────────► [GHOSTED]
     │              │
     ▼              ▼
  [SCREENING]    (no response)
     │
     ▼
  [INTERVIEWING] ──────────────────────────────────► [REJECTED]
     │
     ▼
  [OFFER]
     │              │
     ▼              ▼
  [ACCEPTED]    [REJECTED]

  All transitions logged · Timestamps auto-set via Pipeline
```

---

## Feature Map

| Module | What it does |
|--------|-------------|
| **Profile Store** | Single source of truth. Fill once: personal info, work history, education, skills, certs, projects, links. Supports markdown descriptions, tags, drag-to-reorder. |
| **CV Builder** | Create named CV versions from your profile. Toggle sections, override content per CV, pick a template, preview live in an iframe, export to PDF. |
| **Job Tracker** | Kanban board + table view. Drag cards between status columns. Link each application to a CV version. Track interviews per application. |
| **Analytics** | 10 datasets: application funnel, daily timeline, source breakdown, CV performance table, interview outcomes, activity heatmap, response rate, and more. |
| **Companies** | Normalised company records. All applications grouped by company. |
| **PDF Export** | Queued job via Spatie Browsershot (headless Chrome). Three Blade templates: single-column, two-column, sidebar. |
| **Glassmorphism UI** | Dark mesh background with animated orbs. Glass cards with `backdrop-filter: blur`. Shadcn/ui components restyled for dark glass. |

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend framework | Laravel 13 (PHP 8.4+) |
| Frontend framework | React 19 + TypeScript |
| SPA bridge | Inertia.js v2 |
| Bundler | Vite |
| CSS framework | Tailwind CSS v4 (CSS-first config) |
| UI components | Shadcn/ui |
| Forms | React Hook Form + Zod |
| Charts | Recharts |
| Drag & drop | dnd-kit |
| Rich text | Tiptap |
| PDF generation | Spatie Browsershot (headless Chrome) |
| Activity logging | Spatie Laravel Activitylog |
| Database | PostgreSQL |
| Queue | Laravel Queue (database driver) |
| Auth | Laravel Sanctum |
| Dev tooling | Laravel Telescope + Laravel Pulse |

---

## Database Schema

```
┌─────────────────────┐     ┌──────────────────────┐
│   profile_sections  │     │     cv_templates      │
│─────────────────────│     │──────────────────────│
│ id (bigint PK)      │     │ id (bigint PK)        │
│ type (enum)         │     │ name                  │
│ title               │     │ slug (unique)         │
│ organization        │     │ layout (enum)         │
│ location            │     │ config (json)         │
│ start_date          │     │ thumbnail_path        │
│ end_date            │     │ is_default            │
│ is_current          │     └──────────┬───────────┘
│ description (text)  │                │
│ meta (json)         │     ┌──────────▼───────────┐
│ sort_order          │     │      cv_versions      │
│ is_visible          │     │──────────────────────│
│ deleted_at          │     │ id (bigint PK)        │
└──────────┬──────────┘     │ cv_template_id (FK)  │
           │                │ name                  │
           │  ┌─────────────│ slug (unique)         │
           │  │             │ target_role           │
┌──────────▼──▼──────────┐  │ target_industry      │
│  cv_section_overrides   │  │ is_base              │
│─────────────────────────│  │ last_exported_at     │
│ id (bigint PK)          │  │ export_count         │
│ cv_version_id (FK) ─────┘  │ deleted_at           │
│ profile_section_id (FK) │  └──────────┬───────────┘
│ is_included             │             │
│ sort_order              │  ┌──────────▼───────────┐
│ override_title          │  │   job_applications   │
│ override_description    │  │──────────────────────│
│ override_meta (json)    │  │ id (bigint PK)       │
└─────────────────────────┘  │ company_id (FK)      │
                             │ cv_version_id (FK)   │
┌────────────────────┐       │ role_title           │
│       tags         │       │ job_url              │
│────────────────────│       │ source (enum)        │
│ id (bigint PK)     │       │ salary_min / max     │
│ name               │       │ status (enum)        │
│ slug (unique)      │       │ applied_at           │
│ color              │       │ responded_at         │
└────────────────────┘       │ deadline             │
                             │ excitement_level     │
┌────────────────────┐       │ notes                │
│profile_section_tag │       │ deleted_at           │
│ (pivot)            │       └──────────┬───────────┘
│────────────────────│                  │
│ profile_section_id │    ┌─────────────┼──────────────┐
│ tag_id             │    │             │              │
└────────────────────┘    │             │              │
                          ▼             ▼              ▼
               ┌──────────────┐ ┌────────────┐ ┌──────────────┐
               │  interviews  │ │  companies │ │status_history│
               │──────────────│ │────────────│ │──────────────│
               │ id (bigint)  │ │ id(bigint) │ │ id (bigint)  │
               │ application  │ │ name       │ │ application  │
               │   _id (FK)   │ │ slug       │ │   _id (FK)   │
               │ round        │ │ website    │ │ from_status  │
               │ type (enum)  │ │ industry   │ │ to_status    │
               │ scheduled_at │ │ size(enum) │ │ note         │
               │ outcome(enum)│ │ location   │ │ changed_at   │
               │ feedback     │ │ deleted_at │ └──────────────┘
               │ prep_notes   │ └────────────┘
               │ deleted_at   │
               └──────────────┘
```

**Key design decisions:**
- All PKs are `bigint` (auto-increment) — simple, fast, debuggable
- Soft deletes on: `job_applications`, `cv_versions`, `profile_sections`, `companies`, `interviews`
- Full-text indexes on: `profile_sections(title, description)`, `job_applications(role_title, notes)`, `companies(name, industry)`
- JSON columns for flexible data: `cv_templates.config`, `profile_sections.meta`, `cv_section_overrides.override_meta`
- Status transitions always create a `status_histories` record (enforced via Pipeline)

---

## Installation — With Laravel Sail (Docker)

Laravel Sail gives you a full Docker environment with zero local dependencies (no PHP, no Node, no PostgreSQL needed on your machine).

### Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installed and running
- Git

### Steps

**1. Clone the repository**

```bash
git clone https://github.com/yourname/cv-manager.git
cd cv-manager
```

**2. Copy environment file**

```bash
cp .env.example .env
```

**3. Install Composer dependencies using a temporary Docker container**

> This step is needed because you don't have PHP installed locally yet.

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
```

**4. Configure Sail in `.env`**

Open `.env` and set the following database settings (Sail handles these automatically):

```env
DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=cv_manager
DB_USERNAME=sail
DB_PASSWORD=password

QUEUE_CONNECTION=database
CACHE_STORE=database
SESSION_DRIVER=database
```

**5. Start Sail**

```bash
./vendor/bin/sail up -d
```

This starts: PHP 8.4, PostgreSQL, and Redis containers. First run takes a few minutes to pull images.

**6. Generate application key**

```bash
./vendor/bin/sail artisan key:generate
```

**7. Run migrations and seed**

```bash
./vendor/bin/sail artisan migrate --seed
```

**8. Install Node dependencies and build assets**

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

For development with hot reload:

```bash
./vendor/bin/sail npm run dev
```

**9. Install Telescope (dev tooling)**

```bash
./vendor/bin/sail artisan telescope:install
./vendor/bin/sail artisan migrate
```

**10. Start the queue worker**

```bash
./vendor/bin/sail artisan queue:work
```

**That's it.** Visit [http://localhost](http://localhost)

### Sail Quick Reference

```bash
# Start containers
./vendor/bin/sail up -d

# Stop containers
./vendor/bin/sail down

# Run Artisan commands
./vendor/bin/sail artisan <command>

# Run Composer
./vendor/bin/sail composer <command>

# Run NPM
./vendor/bin/sail npm <command>

# Open a shell inside the container
./vendor/bin/sail shell

# View logs
./vendor/bin/sail logs

# Run tests
./vendor/bin/sail test
```

### Setting up a Sail alias (optional but recommended)

Add this to your `~/.bashrc` or `~/.zshrc` so you can type `sail` instead of `./vendor/bin/sail`:

```bash
alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'
```

Then `source ~/.bashrc` and use `sail up -d`, `sail artisan migrate`, etc.

---

## Installation — Without Sail (Local)

Use this if you prefer a native local setup.

### Prerequisites

Ensure these are installed on your machine:

| Requirement | Version | Check |
|-------------|---------|-------|
| PHP | 8.4+ | `php --version` |
| Composer | 2.x | `composer --version` |
| Node.js | 20+ | `node --version` |
| NPM | 10+ | `npm --version` |
| PostgreSQL | 15+ | `psql --version` |
| Git | any | `git --version` |
| Chrome/Chromium | any | Required for PDF export via Browsershot |

> **Windows users:** Use [WSL2](https://learn.microsoft.com/en-us/windows/wsl/install) with Ubuntu. The experience is significantly better than native Windows for Laravel development.

> **macOS users:** Install PHP and PostgreSQL via [Homebrew](https://brew.sh): `brew install php postgresql@15`

### Steps

**1. Clone the repository**

```bash
git clone https://github.com/yourname/cv-manager.git
cd cv-manager
```

**2. Install PHP dependencies**

```bash
composer install
```

**3. Copy and configure environment**

```bash
cp .env.example .env
```

**4. Create PostgreSQL database**

```bash
# Connect to PostgreSQL
psql -U postgres

# Inside psql:
CREATE DATABASE cv_manager;
CREATE USER cv_user WITH ENCRYPTED PASSWORD 'your_password';
GRANT ALL PRIVILEGES ON DATABASE cv_manager TO cv_user;
\q
```

**5. Update `.env` with your database credentials**

```env
APP_NAME="CV Manager"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=cv_manager
DB_USERNAME=cv_user
DB_PASSWORD=your_password

QUEUE_CONNECTION=database
CACHE_STORE=database
SESSION_DRIVER=database

FILESYSTEM_DISK=local

# Browsershot — path to Node and NPM binaries
BROWSERSHOT_NODE_BINARY=/usr/local/bin/node
BROWSERSHOT_NPM_BINARY=/usr/local/bin/npm

# Laravel Telescope (dev only)
TELESCOPE_ENABLED=true

# Laravel Pulse
PULSE_ENABLED=true
```

**6. Generate application key**

```bash
php artisan key:generate
```

**7. Run migrations and seed**

```bash
php artisan migrate --seed
```

**8. Install Node dependencies**

```bash
npm install
```

**9. Build frontend assets**

```bash
# Production build
npm run build

# OR development with hot reload (keep this running)
npm run dev
```

**10. Install Telescope**

```bash
php artisan telescope:install
php artisan migrate
```

**11. Install Browsershot (for PDF export)**

Browsershot uses Puppeteer under the hood. Install it:

```bash
npm install puppeteer
```

Find your Node and NPM binary paths and add them to `.env`:

```bash
which node   # e.g. /usr/local/bin/node
which npm    # e.g. /usr/local/bin/npm
```

> **Linux:** You may also need: `sudo apt-get install -y chromium-browser`
> **macOS:** Chrome is detected automatically if installed.

**12. Start the development server**

```bash
php artisan serve
```

**13. Start the queue worker** (in a separate terminal)

```bash
php artisan queue:work
```

**14. Start the scheduler** (in a separate terminal, for interview reminders)

```bash
php artisan schedule:work
```

Visit [http://localhost:8000](http://localhost:8000)

---

## Environment Variables

| Variable | Default | Description |
|----------|---------|-------------|
| `APP_URL` | `http://localhost` | Application base URL |
| `DB_CONNECTION` | `pgsql` | Database driver |
| `DB_HOST` | `127.0.0.1` | Database host (`pgsql` for Sail) |
| `DB_DATABASE` | `cv_manager` | Database name |
| `QUEUE_CONNECTION` | `database` | Queue driver |
| `CACHE_STORE` | `database` | Cache store |
| `SESSION_DRIVER` | `database` | Session driver |
| `FILESYSTEM_DISK` | `local` | Where PDF exports are saved |
| `BROWSERSHOT_NODE_BINARY` | `/usr/bin/node` | Path to Node.js binary |
| `BROWSERSHOT_NPM_BINARY` | `/usr/bin/npm` | Path to NPM binary |
| `TELESCOPE_ENABLED` | `true` | Enable Laravel Telescope |
| `PULSE_ENABLED` | `true` | Enable Laravel Pulse |

---

## Running the Application

### Development mode

You need three terminal processes running simultaneously:

```bash
# Terminal 1: Laravel dev server
php artisan serve

# Terminal 2: Vite hot reload
npm run dev

# Terminal 3: Queue worker (for PDF export jobs)
php artisan queue:work
```

With Sail:

```bash
# One command starts everything
sail up -d

# Then in separate terminals:
sail npm run dev
sail artisan queue:work
```

### Production mode

```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Use a process manager (Supervisor) for queue:
php artisan queue:work --daemon
```

---

## Seeded Credentials

After running `php artisan migrate --seed`, one user is created:

| Field | Value |
|-------|-------|
| Email | `admin@cv.local` |
| Password | `password` |

The seeder also creates:
- 3 CV templates (single-column, two-column, sidebar)
- 20 tags (PHP, Laravel, React, TypeScript, Docker, AWS, etc.)
- A realistic profile (5 experiences, 3 educations, 10 skills, 3 certifications)
- 3 CV versions
- 15 companies
- 30 job applications across all statuses (last 6 months)
- 10 interviews with various outcomes

---

## Key Laravel 13 Features Used

| Feature | Where used |
|---------|-----------|
| `#[ObservedBy]` attribute | Models register their own observers declaratively |
| `#[Scope]` attribute | Local query scopes defined without the `scope` prefix |
| PHP 8.4 property hooks | Computed model attributes (e.g. `days_in_current_status`, `is_overdue`) |
| Readonly DTOs | `CreateApplicationDTO`, `StatusTransitionPayload` — type-safe data passing |
| `Concurrency::run()` | AnalyticsService fetches all 10 datasets in parallel |
| `Inertia::defer()` | Heavy analytics props stream in after page paint |
| `casts(): array` method | New syntax replacing `$casts` property |
| Backed enum casts | All status/type columns backed by PHP enums with `label()`, `color()` methods |
| `Model::shouldBeStrict()` | Prevents lazy loading and missing attributes in development |
| Pipeline | Status transitions flow through ordered pipe classes |
| Rate limiting | PDF export limited to 20/hour per user |
| Health checks | `/up` endpoint extended with DB, storage, queue checks |
| `Cache::tags()` | Analytics results cached with tag-based invalidation |
| `LazyCollection` | Memory-efficient processing of large status_history datasets |
| `laravel new --using=react` | Unified starter kit command (new in Laravel 13) |

---


## Analytics Datasets

All computed by `AnalyticsService` using `Concurrency::run()` for parallel fetching. Heavy datasets are deferred via `Inertia::defer()` so the page paints instantly.

| Dataset | Description | Chart |
|---------|-------------|-------|
| `application_funnel` | Count per status stage in order | Horizontal glass bar chart |
| `applications_over_time` | Daily new application count | Line chart with gradient fill |
| `response_rate` | (past "applied" ÷ total) × 100 | Metric card |
| `avg_days_to_response` | Days from `applied_at` to first status change | Metric card |
| `top_companies_by_industry` | Application count by industry | Horizontal bar chart |
| `source_breakdown` | Count per `ApplicationSource` value | Donut pie chart |
| `cv_performance` | Per CV: apps, responses, response rate | Sortable table + mini bars |
| `interview_outcomes` | Passed/failed/pending per `InterviewType` | Grouped bar chart |
| `excitement_vs_outcome` | Avg excitement for accepted vs rejected | Comparison metric |
| `weekly_activity` | Status changes per week for heatmap | Custom SVG 52w × 7d grid |

---

## CV Template System

Templates are Blade views in `resources/views/cv-templates/`. They receive:
- `$cvVersion` — the CV version model
- `$sections` — resolved sections (master profile merged with any overrides)
- `$config` — template config (font, accent color, layout settings) as CSS variables

```
GET /cv-versions/{cv}/preview → Returns rendered Blade view for iframe
POST /cv-versions/{cv}/export-pdf → Dispatches GenerateCvPdfJob → stores PDF → returns download URL
```

| Template | Layout |
|----------|--------|
| `single-column` | Classic top-to-bottom, clean typography, print-optimised |
| `two-column` | Left sidebar (skills, contact) + main content (experience, education) |
| `sidebar` | Gradient accent strip left edge, skill bars, timeline experience entries |

> Templates use **pure CSS only** (no Tailwind) for reliable PDF rendering fidelity via Browsershot.

---

## Queue & Scheduler

### Queue Workers

The queue handles PDF generation (Browsershot). Start a worker:

```bash
# Local
php artisan queue:work

# Sail
sail artisan queue:work

# Production (with Supervisor)
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

### Scheduler

The scheduler handles interview reminders and maintenance:

```bash
# Development (polls every minute)
php artisan schedule:work

# Production (add to crontab)
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

**Scheduled tasks:**

| Task | Frequency | Description |
|------|-----------|-------------|
| `SendInterviewRemindersJob` | Every hour | Sends reminders 24h before interviews |
| `ApplicationDeadlineNotification` | Daily | Alerts 48h before application deadlines |
| `cache:prune-stale-tags` | Daily | Cleans up stale cache tags |
| `telescope:prune --hours=48` | Daily | Keeps Telescope lean in dev |
| `pulse:check` | Every minute | Records Pulse metrics |

---

## Testing

```bash
# Run all tests
php artisan test

# With Sail
sail test

# Run specific test file
php artisan test tests/Feature/ApplicationStatusTest.php

# Run with coverage
php artisan test --coverage

# Run only unit tests
php artisan test --testsuite=Unit
```

### Test coverage targets

- `ApplicationStatusTest` — all valid and invalid Pipeline transitions
- `CvVersionTest` — `resolvedSections()` merge logic with various override combinations
- `AnalyticsTest` — all 10 dataset shapes and date range filtering
- `JobApplicationFilterTest` — filter combinations: status, source, company, date range, search
- `CvExportTest` — Browsershot mocked, job dispatch verified, file storage confirmed

---

## Troubleshooting

**Vite assets not loading / blank page**

```bash
npm run dev   # Make sure Vite dev server is running alongside php artisan serve
```

**PDF export fails**

```bash
# Check Chrome is installed
which chromium-browser   # Linux
which google-chrome      # macOS

# Check Node path in .env matches reality
which node   # Copy this exact path to BROWSERSHOT_NODE_BINARY

# Run Browsershot test
php artisan tinker
>>> \Spatie\Browsershot\Browsershot::url('https://example.com')->save('/tmp/test.pdf');
```

**Queue jobs not processing**

```bash
# Make sure worker is running
php artisan queue:work

# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

**Analytics showing empty / stale data**

```bash
# Clear analytics cache
php artisan cache:clear

# Or target just analytics tag
php artisan tinker
>>> Cache::tags(['analytics'])->flush();
```

**`Model::shouldBeStrict()` throwing lazy loading errors**

This is expected behaviour in development. Fix by eager loading the relationship:

```php
// Instead of:
$application->company->name  // triggers lazy load = exception

// Do:
$application->load('company');
// Or in query:
JobApplication::with('company')->find($id);
```

**PostgreSQL connection refused**

```bash
# Check PostgreSQL is running (local)
sudo service postgresql start    # Linux
brew services start postgresql   # macOS

# Check your DB credentials in .env match your PostgreSQL setup
psql -U cv_user -d cv_manager -h 127.0.0.1
```

**Sail permission errors**

```bash
# Fix storage permissions inside container
sail shell
chmod -R 775 storage bootstrap/cache
```

---

<div align="center">

Built with ❤️ using Laravel 13 · React 19 · Inertia.js v2

</div>
