<?php

namespace App\Models;

use App\Enum\ReportReason;
use App\Enum\ReportStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Override;

class Report extends Model
{
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected $fillable = [
        'slug',
        'reason',
        'status'
    ];

    protected function casts()
    {
        return [
            'reason' => ReportReason::class,
            'status' => ReportStatus::class
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reportable(): MorphTo
    {
        return $this->morphTo()->constrain([
            Comment::class => function ($query) {
                $query->select('id', 'slug');
            },
            Discussion::class => function ($query) {
                $query->select('id', 'slug');
            },
            Game::class => function ($query) {
                $query->select('id', 'slug');
            },
            Review::class => function ($query) {
                $query->select('id', 'slug');
            },
            User::class => function ($query) {
                $query->select('id', 'username');
            },
        ]);
    }

    public function scopeActiveReports(Builder $query): Builder
    {
        return $query->where('status', ReportStatus::OPEN);
    }
}
