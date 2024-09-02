<?php

use App\Http\Resources\BookmarkResource;
use App\Http\Resources\TagResource;
use App\Models\Bookmark;
use App\Models\Tag;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'bookmarks' => BookmarkResource::collection(
            Bookmark::query()
                ->with('tags')
                ->when(request()->input('tags'), function (Builder $query, array $tags) {
                    $query->whereHas('tags', fn (Builder $query) => $query->whereIn('id', $tags));
                })
                ->get()
        ),
        'tags' => TagResource::collection(Tag::all()),
        'filters' => request()->only(['tags']),
    ]);
});
