<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\StoreRequest;
use App\Http\Requests\Comment\UpdateRequest;
use App\Models\Comment;
use App\Models\Discussion;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CommentController extends Controller
{
	public function show(Comment $comment)
	{
		return Inertia::render('app/Comment', [
			'comment' => $comment
		]);
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(StoreRequest $request)
	{
		$data = $request->validated();

		$discussion = Discussion::select('id')->where('slug', $data['discussion_slug'])->first();

		$comment = $discussion->comments()->make([...$data, 'slug' => Str::uuid()]);
		$comment->user()->associate($request->user());

		$path = "discussions/{$data['discussion_slug']}/{$comment->slug}";
		Storage::makeDirectory($path);

		$tempMedia = [];

		if (array_key_exists('media', $data)) {
			foreach ($data['media'] as $i => $file) {
				$fileName = "{$comment->slug}-media-{$i}.{$file->extension()}";
				Storage::putFileAs($path, $file, $fileName);
				$tempMedia[] = $fileName;
			}

			$comment->media = $tempMedia;
		}

		$comment->save();

		return to_route('discussions.show', ['discussion' => $data['discussion_slug']]);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(UpdateRequest $request, Comment $comment)
	{
		$comment->content = $request->post('content');

		$to_remove = $request->post('media_to_delete');
		if ($to_remove) {
			foreach ($to_remove as $file) {
				Storage::delete("discussions/{$comment->discussion->slug}/{$comment->slug}/{$file}");
			}

			$comment->media = array_diff($comment->media, $to_remove);
		}

		$comment->save();

		return back(303);
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Comment $comment)
	{
		foreach ($comment->media as $file) {
			Storage::delete("discussions/{$comment->discussion->slug}/{$comment->slug}/{$file}");
		}
		Storage::deleteDirectory("discussions/{$comment->discussion->slug}/{$comment->slug}");

		$comment->delete();

		return back(303);
	}
}
