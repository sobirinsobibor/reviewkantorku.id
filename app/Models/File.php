<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'disk',
        'path',
        'filename',
        'mime_type',
        'size',
        'uploaded_by',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function fileables()
    {
        return $this->hasMany(Fileable::class);
    }

    public function offices()
    {
        return $this->morphedByMany(Office::class, 'fileable', 'fileables')
            ->withPivot('collection')
            ->withTimestamps();
    }

    public function interactions()
    {
        return $this->morphedByMany(Interaction::class, 'fileable', 'fileables')
            ->withPivot('collection')
            ->withTimestamps();
    }
}
