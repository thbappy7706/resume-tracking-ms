<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CvSectionOverrideController;
use App\Http\Controllers\CvTemplateController;
use App\Http\Controllers\CvVersionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InterviewController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfileSectionController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/sections', [ProfileSectionController::class, 'store'])->name('profile.sections.store');
    Route::put('/profile/sections/{section}', [ProfileSectionController::class, 'update'])->name('profile.sections.update');
    Route::delete('/profile/sections/{section}', [ProfileSectionController::class, 'destroy'])->name('profile.sections.destroy');
    Route::post('/profile/sections/reorder', [ProfileSectionController::class, 'reorder'])->name('profile.sections.reorder');

    // CV Versions
    Route::get('/cv-versions', [CvVersionController::class, 'index'])->name('cv-versions.index');
    Route::post('/cv-versions', [CvVersionController::class, 'store'])->name('cv-versions.store');
    Route::get('/cv-versions/{cv}', [CvVersionController::class, 'show'])->name('cv-versions.show');
    Route::put('/cv-versions/{cv}', [CvVersionController::class, 'update'])->name('cv-versions.update');
    Route::delete('/cv-versions/{cv}', [CvVersionController::class, 'destroy'])->name('cv-versions.destroy');
    Route::post('/cv-versions/{cv}/duplicate', [CvVersionController::class, 'duplicate'])->name('cv-versions.duplicate');
    Route::post('/cv-versions/{cv}/export-pdf', [CvVersionController::class, 'exportPdf'])->name('cv-versions.export-pdf');
    Route::post('/cv-versions/{cv}/overrides', [CvSectionOverrideController::class, 'sync'])->name('cv-versions.overrides.sync');

    // Templates
    Route::get('/templates', [CvTemplateController::class, 'index'])->name('templates.index');
    Route::post('/templates', [CvTemplateController::class, 'store'])->name('templates.store');
    Route::put('/templates/{template}', [CvTemplateController::class, 'update'])->name('templates.update');

    // Applications
    Route::get('/applications', [JobApplicationController::class, 'index'])->name('applications.index');
    Route::post('/applications', [JobApplicationController::class, 'store'])->name('applications.store');
    Route::get('/applications/{application}', [JobApplicationController::class, 'show'])->name('applications.show');
    Route::put('/applications/{application}', [JobApplicationController::class, 'update'])->name('applications.update');
    Route::delete('/applications/{application}', [JobApplicationController::class, 'destroy'])->name('applications.destroy');
    Route::patch('/applications/{application}/status', [JobApplicationController::class, 'updateStatus'])->name('applications.update-status');

    // Interviews (nested under applications)
    Route::post('/applications/{application}/interviews', [InterviewController::class, 'store'])->name('applications.interviews.store');
    Route::put('/applications/{application}/interviews/{interview}', [InterviewController::class, 'update'])->name('applications.interviews.update');
    Route::delete('/applications/{application}/interviews/{interview}', [InterviewController::class, 'destroy'])->name('applications.interviews.destroy');

    // Companies
    Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
    Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
    Route::put('/companies/{company}', [CompanyController::class, 'update'])->name('companies.update');

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
});

require __DIR__.'/settings.php';
