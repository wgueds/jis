<?php

namespace App\Enums;

enum ReleaseMethodEnum: int
{
    case SINGLE = 1;
    case INSTALLMENTS = 2;
    case FIXED = 3;
}