<?php

namespace App\Enums;

enum InterviewOutcome: string
{
    case Pending = 'pending';
    case Successful = 'successful';
    case Unsuccessful = 'unsuccessful';
}
