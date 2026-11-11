<?php

namespace App\Http\Controllers;

use App\Enum\UserRole;
use App\Http\Requests\User\UserListFilterRequest;
use App\Http\Resources\User\SimpleProfileResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Inertia\Inertia;
use Inertia\Response;

class UserListController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(UserListFilterRequest $request): Response
    {
        $filters = $request->validated();

        $users = User::select(['name', 'username', 'role', 'avatar'])
            ->orderBy('name', 'ASC')
            ->when($filters['contains'] ?? null, fn (Builder $query, string $contains) => $query->where(
                fn (Builder $q) => $q->whereLike('name', "%{$contains}%")
                        ->orWhereLike('username', "%{$contains}%")
            ))
            ->when($filters['role'] ?? null, fn (Builder $query, string $role) => $query->where('role', $role))
            ->paginate(30);

        return Inertia::render('app/Users', [
            'users' => Inertia::defer(fn () => SimpleProfileResource::collection($users)),
            'roles' => array_filter(array_reverse(array_column(UserRole::cases(), 'value')), fn ($r) => $r !== UserRole::USER),
        ]);
    }
}
