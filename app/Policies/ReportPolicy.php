<?php

namespace App\Policies;

use App\Enum\UserRole;
use App\Models\Report;
use App\Models\User;

class ReportPolicy
{
    public function delete(User $user, Report $report): bool
    {
        return in_array($user->role, [UserRole::ADMIN, UserRole::DEVELOPER]);
    }
}
