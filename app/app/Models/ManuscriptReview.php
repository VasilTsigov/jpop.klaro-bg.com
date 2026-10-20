<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ManuscriptReview extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['invited_at' => 'datetime', 'deadline_at' => 'datetime', 'confirmed_at' => 'datetime', 'reviewed_at' => 'datetime'];
    }

    public function manuscript(): BelongsTo
    {
        return $this->belongsTo(Manuscript::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(ReviewInvitation::class, 'review_id');
    }

    /** A slot counts as taken once someone was invited and has not declined. */
    public function isActive(): bool
    {
        return $this->reviewer_id !== null && $this->confirmed_state !== 'declined';
    }
}
