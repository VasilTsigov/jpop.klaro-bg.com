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
