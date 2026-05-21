<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class Interaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'office_id',
        'user_id',
        'ulid',
        'first_parent_id',
        'direct_parent_id',
        'reply_to',
        'attributes',
        'experience',
        'positive_notes',
        'is_anonymous',
        'is_hidden',
        'reported_at',
        'type'
    ];

    protected $casts = [
        'attributes' => 'array', 
        'is_anonymous' => 'boolean',
        'is_hidden' => 'boolean',
        'reported_at' => 'datetime',
    ];

    public function getRouteKeyName()
    {
        return 'ulid';
    }

    // Ambil "main content" berdasarkan type
    public function getMainContentsAttribute(): array
    {
        $fields = config('interaction_fields.' . $this->type . '.main_contents', []);
        $labels = config('interaction_fields.' . $this->type . '.labels', []);
        $attrs  = collect(json_decode($this->getAttributes()['attributes'] ?? '[]', true));

        return collect($fields)
            ->mapWithKeys(fn($field) => [
                $field => [
                    'label' => $labels[$field] ?? $field,
                    'value' => $attrs->firstWhere('name', $field)['userData'][0] ?? null,
                ]
            ])
            ->all();
    }
    
    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function fileables()
    {
        return $this->morphMany(Fileable::class, 'fileable');
    }

    public function files()
    {
        return $this->morphToMany(File::class, 'fileable', 'fileables')
            ->withPivot('collection')
            ->withTimestamps();
    }

    public function reviewPhotos()
    {
        return $this->files()
            ->wherePivot('collection', 'review_photos');
    }

    public function menfessPhotos()
    {
        return $this->files()
            ->wherePivot('collection', 'menfess');
    }

    public function ceritaMagangPhotos()
    {
        return $this->files()
            ->wherePivot('collection', 'cerita_magang');
    }

    public function firstParent()
    {
        return $this->belongsTo(Interaction::class, 'first_parent_id');
    }

    public function directParent()
    {
        return $this->belongsTo(Interaction::class, 'direct_parent_id');
    }

    public function directReplies()
    {
        return $this->hasMany(Interaction::class, 'direct_parent_id','ulid')->latest();
    }

    // Interaction.php
    public function likes()
    {
        return $this->belongsToMany(
            User::class,
            'interaction_likes', // pivot table
            'interaction_id',
            'user_id'
        )->withTimestamps();
    }

    public function getLikesCountAttribute(): int
    {
        return $this->likes()->count();
    }

    public function isLikedBy(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }
}
