<?php

namespace App\Enums;

enum UserRole: string {
    case ADMIN = 'admin';
    case SUPPORT_AGENT = 'support_agent';
    case EMPLOYEE = 'employee';
}
