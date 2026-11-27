<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\StoreRequest;
use App\Http\Requests\Comment\UpdateRequest;
use App\Http\Resources\Comment\EditCommentResource;
use App\Http\Resources\Comment\ShowCommentResource;
use App\Models\Comment;
use App\Models\Discussion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
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
        $slug = (string) Str::orderedUuid();
        $discussion = Discussion::select(['id', 'slug'])->where('slug', $data['discussion_slug'])->firstOrFail();
        $path = "discussions/{$discussion->slug}/{$slug}";

        try {
            $comment = $discussion->comments()->make(['slug' => $slug, 'content' => $data['content'], 'media' => []]);
            $comment->user()->associate($request->user());

            $media = [];
            foreach ($data['media'] ?? [] as $file) {
                $name = Str::orderedUuid().".{$file->extension()}";
                Storage::putFileAs($path, $file, $name);
                $media[] = $name;
            }

            $comment->media = $media;
            $comment->save();
        } catch (\Throwable $e) {
            Storage::deleteDirectory($path);
            report($e);

            throw ValidationException::withMessages([
                'media' => 'Media upload failed. Please try again.'
            ]);
        }

        return to_route('discussions.show', ['discussion' => $data['discussion_slug']]);
    }

    public function edit(Comment $comment): Response
    {
        Gate::authorize('update', $comment);

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
        Gate::authorize('update', $comment);

        $data = $request->validated();

        $comment->load('discussion:id,slug');

        $path = "discussions/{$comment->discussion->slug}/{$comment->slug}";
        $current = $comment->media ?? [];
        $toDelete = array_values(array_intersect($data['media_to_delete'] ?? [], $current));
        $media = array_values(array_diff($current, $toDelete));
        $obsolete = array_map(fn (string $file) => "{$path}/{$file}", $toDelete);

        $comment->update([
            'content' => $data['content'],
            'media' => $media
        ]);

        try {
            Storage::delete($obsolete);
            if (count($media) === 0) {
                Storage::deleteDirectory($path);
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return $request->has('back_to_discussion')
            ? to_route('discussions.show', ['discussion' => $comment->discussion->slug])
            : to_route('comments.show', ['comment' => $comment->slug]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment): RedirectResponse
    {
        Gate::authorize('delete', $comment);

        $path = "discussions/{$comment->discussion->slug}/{$comment->slug}";

        DB::transaction(function () use ($comment) {
            $comment->reports()->delete();
            $comment->delete();
        });

        try {
            Storage::deleteDirectory($path);
        } catch (\Throwable $e) {
            report($e);
        }

        return request()->has('to_homepage')
            ? to_route('home')
            : back();
    }
}
