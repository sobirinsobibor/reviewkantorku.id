<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentForm extends Model
{
    protected $fillable = [
        'type',
        'name',
        'schema',
        'is_active',
        'version',
    ];

    protected $casts = [
        'schema' => 'array',
        'is_active' => 'boolean',
    ];
}
