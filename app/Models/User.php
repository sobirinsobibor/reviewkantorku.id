<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Filament\Panel;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_admin;
    }

    protected $fillable = [
        'name', 'email', 'is_active', 'is_admin', 'password', 'username', 'profile_photo_path', 'ulid'
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getRouteKeyName()
    {
        return 'ulid';
    }


    protected static function booted()
    {
        static::creating(function ($office) {
           
            //ulid
            if(empty($office->ulid)){
                $office->ulid = (string) Str::ulid();
            }
        });
    }

    public function offices(): HasMany
    {
        return $this->hasMany(Office::class, 'created_by');
    }

    public function interactions(): HasMany
    {
        return $this->hasMany(Interaction::class);
    }

    // User.php
    public function likedInteractions()
    {
        return $this->belongsToMany(Interaction::class, 'interaction_likes')->withTimestamps();
    }
}
