<?php

namespace App\Enums;

enum Status:string
{
    case BLOCKED = 'blocked';
    case UNBLOCKED = 'unblocked';
}
