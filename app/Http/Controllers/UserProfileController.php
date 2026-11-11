<?php

namespace App\Http\Controllers;

use App\Enum\UserProfileTab;
use App\Http\Resources\User\UserProfileResource;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class UserProfileController extends Controller
{
    public function __invoke(User $user, UserProfileTab $tab = UserProfileTab::CREATED_GAMES): Response
    {
        return Inertia::render('app/UserProfile', [
            'user' => UserProfileResource::make($user),
            'tabs' => array_map(
                fn (UserProfileTab $t) => [
                    'name'  => $t->value,
                    'route' => route('user.profile', ['user' => $user, 'tab' => $t]),
                ],
                UserProfileTab::cases()
            ),
            'activeTab' => $tab->value
        ]);
    }
}
