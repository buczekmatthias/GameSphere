<?php

namespace App\Policies;

use App\Models\Discussion;
use App\Models\User;

class DiscussionPolicy
{
	/**
	 * Determine whether the user can update the model.
	 */
	public function update(User $user, Discussion $discussion): bool
	{
		if (is_null($user)) {
			return false;
		}

		if ($user->isStaff()) {
			return true;
		}

		if (is_null($discussion->user_id)) {
			return false;
		}

		return $discussion->user_id === $user->id;
	}

	/**
	 * Determine whether the user can delete the model.
	 */
	public function delete(User $user, Discussion $discussion): bool
	{
		if (is_null($user)) {
			return false;
		}

		if ($user->isStaff()) {
			return true;
		}

		if (is_null($discussion->user_id)) {
			return false;
		}

		return $discussion->user_id === $user->id;
	}
}
