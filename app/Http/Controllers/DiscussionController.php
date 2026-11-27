<?php

namespace App\Http\Controllers;

use App\Http\Requests\Discussion\StoreRequest;
use App\Http\Requests\Discussion\UpdateRequest;
use App\Http\Resources\Discussion\ListDiscussionResource;
use App\Http\Resources\Discussion\ShowDiscussionResource;
use App\Models\Comment;
use App\Models\Discussion;
use App\Models\Game;
use App\Models\Genre;
use App\Models\Report;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class DiscussionController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('app/discussion/Index', [
            'discussions' => Inertia::defer(fn () => ListDiscussionResource::collection(
                Discussion::with([
                    'author:id,avatar,username,name', 'discussable'
                ])
                    ->when(
                        request()->has('title'),
                        fn (Builder $query) => $query->whereLike('title', "%".request()->get('title')."%")
                    )
                    ->withCount('comments')
                    ->orderBy('created_at', 'DESC')
                    ->orderBy('id', 'ASC')
                    ->paginate(15)
            ))
        ]);
    }

    public function create(string $type, string $slug): Response
    {
        $item = match($type) {
            'game' => Game::select(['title', 'slug'])->where('slug', $slug)->firstOrFail(),
            'genre' => Genre::select(['slug', 'name'])->where('slug', $slug)->firstOrFail(),
        };

        return Inertia::render('app/discussion/Create', [
            'type' => $type,
            'item' => $item
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request): RedirectResponse
    {
        $userId = $request->user()->id;
        $data = $request->validated();
        $slug = (string) Str::orderedUuid();
        $path = "discussions/{$slug}";
        $stored = [];
        $discussable = match($data['type']) {
            'games' => Game::select(['id'])->where('slug', $data['slug'])->firstOrFail(),
            'genres' => Genre::select(['id'])->where('slug', $data['slug'])->firstOrFail()
        };

        try {
            $discussion = DB::transaction(function () use ($data, $discussable, $path, $slug, $userId, &$stored) {
                $discussion = $discussable
                    ->discussions()
                    ->make([
                        'slug' => $slug,
                        'title' => $data['title']
                    ]);
                $discussion->user_id = $userId;
                $discussion->save();

                $commentSlug = (string) Str::orderedUuid();
                $comment = $discussion->comments()->make([
                    'slug' => $commentSlug,
                    'content' => $data['content']
                ]);
                $comment->user_id = $userId;

                $media = [];
                $commentPath = "{$path}/{$commentSlug}";

                foreach ($data['media'] ?? [] as $file) {
                    $name = (string) Str::orderedUuid().".{$file->extension()}";
                    Storage::putFileAs($commentPath, $file, $name);
                    $stored[] = "{$commentPath}/{$name}";
                    $media[] = $name;
                }

                $comment->media = $media;
                $comment->save();

                return $discussion;
            });
        } catch (\Throwable $e) {
            Storage::delete($stored);
            Storage::deleteDirectory($path);
            report($e);

            throw ValidationException::withMessages([
                'media' => 'Media upload failed. Please try again.'
            ]);
        }

        return to_route('discussions.show', ['discussion' => $discussion->slug]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Discussion $discussion): Response
    {
        $discussion->load([
            'author:id,avatar,name,username',
            'discussable'
        ])
            ->loadCount('comments');

        return Inertia::render('app/discussion/Show', [
            'discussion' => ShowDiscussionResource::make($discussion),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Discussion $discussion, UpdateRequest $request): RedirectResponse
    {
        Gate::authorize('update', $discussion);

        $discussion->update($request->validated());

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Discussion $discussion): RedirectResponse
    {
        Gate::authorize('delete', $discussion);

        $path = "discussions/{$discussion->slug}";

        DB::transaction(function () use ($discussion) {
            Report::where('reportable_type', (new Comment)->getMorphClass())
                ->whereIn('reportable_id', $discussion->comments()->pluck('id'))
                ->delete();
            $discussion->reports()->delete();
            $discussion->delete();
        });

        try {
            Storage::deleteDirectory($path);
        } catch (\Throwable $e) {
            report($e);
        }

        return request()->has('return_back')
            ? back()
            : to_route('discussions.index');
    }

    public function toggleLock(Discussion $discussion): RedirectResponse
    {
        Gate::authorize('lock', $discussion);

        $discussion->update(['is_locked' => !$discussion->is_locked]);

        return back();
    }
}
