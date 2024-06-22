<?php

namespace App\Enums;

enum StatusEnum: int
{
    case OPEN = 1;
    case SCHEDULED = 2;
    case PAID = 3;
    case OVERDATE = 4;
}