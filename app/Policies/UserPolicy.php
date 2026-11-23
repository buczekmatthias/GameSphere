<?php

namespace App\Policies;

use App\Enum\UserRole;
use App\Models\User;

class UserPolicy
{
    public function updateRole(User $user, User $model): bool
    {
        return $this->currentUserHasHigherRoleWeight($user, $model);
    }

    public function delete(User $user, User $model): bool
    {
        return $this->currentUserHasHigherRoleWeight($user, $model);
    }

    private function currentUserHasHigherRoleWeight(User $currentUser, User $otherUser): bool
    {
        $weights = collect(array_column(UserRole::cases(), 'value'))
            ->mapWithKeys(fn (string $role, int $key) => [$role => $key + 1])
            ->reverse()
            ->toArray();

        return $weights[$currentUser->role->value] > $weights[$otherUser->role->value];
    }
}
