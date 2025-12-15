<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
	/**
	 * Determine whether the user can delete the model.
	 */
	public function delete(User $user, Review $review): bool
	{
		if (is_null($user)) {
			return false;
		}

		if ($user->isStaff()) {
			return true;
		}

		if (is_null($review->user_id)) {
			return false;
		}

		return $review->user_id === $user->id;
	}
}
