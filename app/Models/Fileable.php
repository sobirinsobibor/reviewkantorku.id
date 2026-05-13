<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fileable extends Model
{
    protected $fillable = [
        'file_id',
        'fileable_id',
        'fileable_type',
        'collection',
    ];

    public function fileable()
    {
        return $this->morphTo();
    }

    public function file()
    {
        return $this->belongsTo(File::class);
    }
}
