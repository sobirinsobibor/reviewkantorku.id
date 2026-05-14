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
        // return $fields;
        $attrs = collect($this->attributes_parsed ?? []); // ← fix di sini

        return collect($fields)
            ->mapWithKeys(fn($field) => [
                $field => $attrs->firstWhere('name', $field)['userData'] ?? 'gaada'
            ])
            ->all();
    }

    public function getMainTitleAttribute(): ?string
    {
        $attrs = collect($this->attributes_parsed);

        return match($this->type) {
            'qna'          => $attrs->firstWhere('name', 'content')['userData'][0] ?? null,
            'cerita_magang'=> $attrs->firstWhere('name', 'title')['userData'][0] ?? null,
            'review'    => $attrs->firstWhere('name', 'experience')['userData'][0] ?? null,
            default        => null,
        };
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

    public function parent()
    {
        return $this->belongsTo(Interaction::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Interaction::class, 'parent_id')->latest();
    }
}
