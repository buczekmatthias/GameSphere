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
		return $this->canModifyDiscussion($user, $discussion);
	}

	/**
	 * Determine whether the user can delete the model.
	 */
	public function delete(User $user, Discussion $discussion): bool
	{
		return $this->canModifyDiscussion($user, $discussion);
	}

	public function lock(User $user, Discussion $discussion): bool
	{
		return $this->canModifyDiscussion($user, $discussion);
	}

	private function canModifyDiscussion(User $user, Discussion $discussion): bool
	{
		if ($user->isStaff()) {
			return true;
		}

		if (is_null($discussion->user_id)) {
			return false;
		}

		return $discussion->user_id === $user->id;
	}
}
