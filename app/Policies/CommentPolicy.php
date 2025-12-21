<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
	/**
	 * Determine whether the user can update the model.
	 */
	public function update(User $user, Comment $comment): bool
	{
		return $this->canModifyComment($user, $comment);
	}

	/**
	 * Determine whether the user can delete the model.
	 */
	public function delete(User $user, Comment $comment): bool
	{
		return $this->canModifyComment($user, $comment);
	}

	private function canModifyComment(User $user, Comment $comment): bool
	{
		if ($user->isStaff()) {
			return true;
		}

		if (is_null($comment->user_id)) {
			return false;
		}

		return $comment->user_id === $user->id;
	}
}
