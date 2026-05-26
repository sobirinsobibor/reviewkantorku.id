<?php

namespace App\Models;

use App\Models\OfficeReaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Office extends Model
{
    protected $fillable = [
        'name',
        'ulid',
        'slug',
        'province_id',
        'regency_id',
        'address',
        'status',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'created_by'
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public static function generateSlug(string $name, ?int $excludeId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 2;

        while (
            self::where('slug', $slug)
                ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    protected static function booted()
    {
        static::creating(function ($office) {
            if (empty($office->slug)) {
                $office->slug = self::generateSlug($office->name);
            }
            if (empty($office->created_by) && Auth::check()) {
                $office->created_by = Auth::id();
            }
            if (empty($office->ulid)) {
                $office->ulid = (string) Str::ulid();
            }
        });
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'approved' => 'Terverifikasi',
            'pending'  => 'Menunggu',
            'rejected' => 'Ditolak',
            default    => '-',
        };
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function interactions()
    {
        return $this->hasMany(Interaction::class);
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

    public function officePhotos()
    {
        return $this->files()
            ->wherePivot('collection', 'office_photos');
    }

    public function industryOffices()
    {
        return $this->hasMany(IndustryOffice::class);
    }

    public function industries()
    {
        return $this->belongsToMany(Industry::class, 'industry_office')
            ->withTimestamps();
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }

    public function regency()
    {
        return $this->belongsTo(Regency::class, 'regency_id', 'id');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(OfficeReaction::class);
    }

    public function likes(): HasMany
    {
        return $this->reactions()->where('type', 'like');
    }

    public function dislikes(): HasMany
    {
        return $this->reactions()->where('type', 'dislike');
    }

    public function isLikedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->reactions()
            ->where('user_id', $user->id)
            ->where('type', 'like')
            ->exists();
    }

    public function isDislikedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->reactions()
            ->where('user_id', $user->id)
            ->where('type', 'dislike')
            ->exists();
    }

    public function getLikesCountAttribute(): int
    {
        return $this->reactions()
            ->where('type', 'like')
            ->count();
    }

    public function getDislikesCountAttribute(): int
    {
        return $this->reactions()
            ->where('type', 'dislike')
            ->count();
    }    
}
