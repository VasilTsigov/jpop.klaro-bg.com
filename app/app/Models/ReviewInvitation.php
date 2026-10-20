<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewInvitation extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['expires_at' => 'datetime', 'used_at' => 'datetime'];
    }

    public function review(): BelongsTo
    {
        return $this->belongsTo(ManuscriptReview::class, 'review_id');
    }

    public static function hash(string $token): string
    {
        return hash('sha256', $token);
    }
}
