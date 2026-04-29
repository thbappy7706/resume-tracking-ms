<?php

namespace App\Enums;

enum InterviewType: string
{
    case Phone = 'phone';
    case Video = 'video';
    case Onsite = 'onsite';
    case TakeHome = 'take_home';
}
