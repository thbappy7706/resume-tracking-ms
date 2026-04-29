<?php

namespace App\Enums;

enum ApplicationSource: string
{
    case Referral = 'referral';
    case JobBoard = 'job_board';
    case CompanyWebsite = 'company_website';
    case Recruiter = 'recruiter';
    case Other = 'other';
}
