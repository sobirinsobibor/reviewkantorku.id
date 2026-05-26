<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeReaction extends Model
{
    protected $table = 'office_likes';

    protected $fillable = [
        'user_id',
        'office_id',
        'type',
    ];

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}