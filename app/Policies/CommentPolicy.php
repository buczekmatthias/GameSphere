<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
	/**
	 * Determine whether the user can create models.
	 */
	public function create(User $user): bool
	{
		return !is_null($user);
	}

	/**
	 * Determine whether the user can update the model.
	 */
	public function update(User $user, Comment $comment): bool
	{
		if (is_null($user)) {
			return false;
		}

		if ($user->isStaff()) {
			return true;
		}

		if (is_null($comment->user_id)) {
			return false;
		}

		return $comment->user_id === $user->id;
	}

	/**
	 * Determine whether the user can delete the model.
	 */
	public function delete(User $user, Comment $comment): bool
	{
		if (is_null($user)) {
			return false;
		}

		if ($user->isStaff()) {
			return true;
		}

		if (is_null($comment->user_id)) {
			return false;
		}
		dd($user. $comment);

		return $comment->user_id === $user->id;
	}
}
