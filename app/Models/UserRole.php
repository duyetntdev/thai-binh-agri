<?php

namespace App\Models;

enum UserRole: string
{
    case ADMIN = 'admin';
    case CUSTOMER = 'customer';
}
