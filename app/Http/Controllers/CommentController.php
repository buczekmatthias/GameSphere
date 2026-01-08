<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\StoreRequest;
use App\Http\Requests\Comment\UpdateRequest;
use App\Http\Resources\Comment\EditCommentResource;
use App\Http\Resources\Comment\ShowCommentResource;
use App\Models\Comment;
use App\Models\Discussion;
use App\Services\ManageMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CommentController extends Controller
{
	public function show(Comment $comment): Response
	{
		$comment->load([
			'user:id,name,username,avatar',
			'discussion:id,title,created_at,slug,user_id',
			'discussion.author:id,name,username,avatar'
		]);

		return Inertia::render('app/comment/Show', [
			'comment' => ShowCommentResource::make($comment),
		]);
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(StoreRequest $request): RedirectResponse
	{
		$data = $request->validated();

		$discussion = Discussion::select(['id', 'slug'])->where('slug', $data['discussion_slug'])->first();

		$comment = $discussion->comments()->make([...$data, 'slug' => Str::uuid()]);
		$comment->user()->associate($request->user());

		$comment->media = ManageMedia::storeCommentMedia(
			$discussion->slug,
			$comment->slug,
			$data['media']
		);

		$comment->save();

		return to_route('discussions.show', ['discussion' => $data['discussion_slug']]);
	}

	public function edit(Comment $comment): Response
	{
		$comment->load([
			'discussion:id,title,slug'
		]);

		return Inertia::render('app/comment/Edit', [
			'comment' => EditCommentResource::make($comment)
		]);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(UpdateRequest $request, Comment $comment): RedirectResponse
	{
		$comment->content = $request->post('content');

		$comment->media = ManageMedia::updateMedia(
			"discussions/{$comment->discussion->slug}/{$comment->slug}",
			$comment->media,
			$request->post('media_to_delete', []),
		);

		$comment->save();

		return $request->has('back_to_discussion')
		? to_route('discussions.show', ['discussion' => $comment->discussion->slug])
		: to_route('comments.show', ['comment' => $comment->slug]);
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Comment $comment): RedirectResponse
	{
		$media = $comment->media;
		$path = "discussions/{$comment->discussion->slug}/{$comment->slug}";

		DB::transaction(function () use ($comment) {
			$comment->reports()->delete();

			$comment->delete();
		});

		ManageMedia::deleteDirectoryWithMedia($path, $media);

		if (request()->has('to_homepage')) {
			return to_route('home', status: 303);
		}

		return back(303);
	}
}
