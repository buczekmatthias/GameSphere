<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use App\Models\GameCreatorRequest;
use App\Services\UserDeleteServices;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('app/settings/Profile', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => $request->session()->get('status'),
            'appliedToBecomeGameCreator' => $request->user()->role === 'user' ? GameCreatorRequest::where('user_id', $request->user()->id)->exists() : true
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return to_route('settings.profile.edit');
    }

    /**
     * Delete the user's profile.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        UserDeleteServices::deleteUser($user);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function updateAvatar(Request $request): RedirectResponse
    {
        $avatar = $request->validate([
            'avatar' => ['image', 'required', "mimes:jpg,png", 'max:20000'],
        ])['avatar'];

        $user = $request->user();
        $name = Str::orderedUuid().".{$avatar->extension()}";
        $oldAvatar = $user->avatar;

        try {
            Storage::putFileAs("users", $avatar, $name);
            $user->update(['avatar' => $name]);
        } catch (\Throwable $e) {
            Storage::delete("users/{$name}");
            report($e);

            throw ValidationException::withMessages([
                'avatar' => 'Could not save your picture. Please try again.',
            ]);
        }

        if ($oldAvatar) {
            try {
                Storage::delete("users/{$oldAvatar}");
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return to_route('settings.profile.edit');
    }

    public function removeAvatar(): RedirectResponse
    {
        $user = request()->user();
        $avatar = $user->avatar;

        if ($avatar) {
            $user->update(['avatar' => null]);

            try {
                Storage::delete("users/{$avatar}");
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return to_route('settings.profile.edit');
    }
}
