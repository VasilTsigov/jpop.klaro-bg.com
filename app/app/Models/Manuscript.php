<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Manuscript extends Model
{
    // id = legacy mscrID
    public $incrementing = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime', 'corrected_at' => 'datetime', 'editor_letter_at' => 'datetime',
            'proof_at' => 'datetime', 'proof_editor_at' => 'datetime',
        ];
    }

    /** "JPOP"+code, the same value the old mscrCodeU column held. */
    public function getCodeLabelAttribute(): string
    {
        return 'JPOP'.$this->code;
    }

    /** LEGACY colorRecommendation(): red when at least two reviewers recommend "Rejected", otherwise yellow. */
    public function getRecommendationColorAttribute(): string
    {
        $rejected = $this->reviews->where('recommendation', 'Rejected')->count();

        return $rejected >= 2 ? '#FF0000' : '#FEED77';
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(ManuscriptFile::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ManuscriptReview::class);
    }
}
