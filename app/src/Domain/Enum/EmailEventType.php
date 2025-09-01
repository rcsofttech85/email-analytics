<?php

namespace App\Domain\Enum;

enum EmailEventType: string
{
    case OPEN = 'open';
    case CLICK = 'click';
}
