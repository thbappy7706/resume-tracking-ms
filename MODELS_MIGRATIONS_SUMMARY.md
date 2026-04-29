# CV Management System - Migrations & Models Summary

## ✅ Completed Deliverables

### Database Migrations (11 files)
All migrations use ULID primary keys and follow Laravel 13 best practices:

1. **Tags** - Skills and section categorization  
2. **CV Templates** - Layout templates with JSON config (font, colors, spacing)
3. **Profile Sections** - Experience, education, skills with soft deletes
4. **Profile Section Tags** (Pivot) - Many-to-many relationship
5. **CV Versions** - Template instances with target role/industry
6. **CV Section Overrides** - Per-version customization with encrypted metadata
7. **Companies** - Job posting sources with full-text search
8. **Job Applications** - Status tracking with salary range and excitement level
9. **Status Histories** - Application timeline tracking
10. **Interviews** - Round-based tracking with outcomes and prep notes

**Migration Locations:**
- `database/migrations/2026_04_29_000000_create_tags_table.php`
- `database/migrations/2026_04_29_000010_create_cv_templates_table.php`
- `database/migrations/2026_04_29_000020_create_profile_sections_table.php`
- `database/migrations/2026_04_29_000030_create_profile_section_tag_table.php`
- `database/migrations/2026_04_29_000040_create_cv_versions_table.php`
- `database/migrations/2026_04_29_000050_create_cv_section_overrides_table.php`
- `database/migrations/2026_04_29_000060_create_companies_table.php`
- `database/migrations/2026_04_29_000070_create_job_applications_table.php`
- `database/migrations/2026_04_29_000080_create_status_histories_table.php`
- `database/migrations/2026_04_29_000090_create_interviews_table.php`

### Eloquent Models (11 files)
All models implement Laravel 13 best practices:

**Core Features:**
- `HasUlids` trait on all models
- `SoftDeletes` on models with temporal concerns
- `#[Fillable(...)]` attributes (instead of `$fillable` property)
- `#[ObservedBy(ModelObserver::class)]` for event observation
- `protected function casts(): array` (new Laravel 13 syntax)
- PHP 8.4 property hooks for computed values
- `#[Scope]` attributes for query builder scopes

**Model Relationships:**

| Model | Relations |
|-------|-----------|
| **Tag** | `belongsToMany(ProfileSection)` |
| **CvTemplate** | `hasMany(CvVersion)` |
| **ProfileSection** | `belongsToMany(Tag)`, `hasMany(CvSectionOverride)` |
| **CvVersion** | `belongsTo(CvTemplate)`, `hasMany(CvSectionOverride)`, `hasMany(JobApplication)` |
| **CvSectionOverride** | `belongsTo(CvVersion)`, `belongsTo(ProfileSection)` |
| **Company** | `hasMany(JobApplication)` |
| **JobApplication** | `belongsTo(Company)`, `belongsTo(CvVersion)`, `hasMany(Interview)`, `hasMany(StatusHistory)` |
| **StatusHistory** | `belongsTo(JobApplication)` |
| **Interview** | `belongsTo(JobApplication)` |

**Model Locations:**
- `app/Models/Tag.php`
- `app/Models/CvTemplate.php`
- `app/Models/ProfileSection.php`
- `app/Models/CvSectionOverride.php`
- `app/Models/CvVersion.php`
- `app/Models/Company.php`
- `app/Models/JobApplication.php`
- `app/Models/StatusHistory.php`
- `app/Models/Interview.php`

### Enums (8 files)
Type-safe domain modeling:

- `app/Enums/CvLayout.php` - Classic, Modern, Minimal
- `app/Enums/SectionType.php` - Experience, Education, Skill, Summary
- `app/Enums/CompanySize.php` - Small, Medium, Large
- `app/Enums/ApplicationSource.php` - Referral, JobBoard, CompanyWebsite, Recruiter, Other
- `app/Enums/ApplicationStatus.php` - Saved, Applied, Interviewing, Offer, Rejected, Closed
- `app/Enums/InterviewType.php` - Phone, Video, Onsite, TakeHome
- `app/Enums/InterviewOutcome.php` - Pending, Successful, Unsuccessful

### Observer (1 file)
- `app/Observers/ModelObserver.php` - Base observer for all models

### Key Model Features

**CvVersion computed properties:**
```php
public int $application_count { get => $this->job_applications_count ?? 0; }
public string $preview_url { get => route('cv-versions.preview', $this); }
public Collection resolvedSections() // Returns merged sections with override data
```

**ProfileSection computed properties:**
```php
public string $date_range { get => $this->formatDateRange(); }

// Scopes:
#[Scope] byType(SectionType $type)
#[Scope] visible()
#[Scope] ordered()
```

**JobApplication computed properties:**
```php
public int $days_in_current_status { get => ... }
public bool $is_overdue { get => $this->deadline?->isPast() ?? false; }
public ?string $salary_range_display { get => ... }

// Scopes:
#[Scope] active()
#[Scope] byStatus(ApplicationStatus $status)
#[Scope] appliedBetween(string $from, string $to)
#[Scope] withFullDetails()
#[Scope] overdueDeadline()
```

**Company computed properties:**
```php
public int $active_applications_count { get => ... }
```

**Interview computed properties:**
```php
public bool $is_upcoming { get => $this->scheduled_at?->isFuture() ?? false; }
public string $duration_display { get => ... }
```

## Database Features Implemented

✅ **ULID Primary Keys** - All tables use ULID for distributed ID generation
✅ **Full-Text Indexing** - ProfileSection (title, description), Company (name, industry), JobApplication (role_title, notes)
✅ **Encrypted Casting** - ProfileSection.meta and CvSectionOverride.override_meta use `AsEncryptedArrayObject`
✅ **Soft Deletes** - ProfileSection, Company, JobApplication, Interview, CvVersion
✅ **Foreign Keys** - Properly defined with cascadeOnDelete/nullOnDelete/restrictOnDelete
✅ **JSON Columns** - CvTemplate.config for flexible template configuration
✅ **Enums** - All status/type fields use database enums

## Code Quality

✅ Formatted with Laravel Pint  
✅ Uses #[Fillable], #[ObservedBy], #[Scope] attributes (PHP 8.4 style)  
✅ Type hints on all methods and properties  
✅ PHP 8.4 property hooks for computed values  
✅ Consistent naming conventions throughout  

## Next Steps

To use these migrations and models:

```bash
# 1. Run migrations
php artisan migrate

# 2. Create factories for testing
php artisan make:factory TagFactory --model=Tag
# ... repeat for other models

# 3. Create tests
php artisan make:test ModelRelationshipsTest --feature

# 4. Use in your application
$cv = CvVersion::with(['cvTemplate', 'jobApplications'])->first();
$resolved = $cv->resolvedSections(); // Merged with overrides
```

All models are ready for Inertia React frontend consumption via API routes.
