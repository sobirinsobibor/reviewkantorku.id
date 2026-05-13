<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    public function industryOffices()
    {
        return $this->hasMany(IndustryOffice::class);
    }

    public function offices()
    {
        return $this->belongsToMany(Office::class, 'industry_office')
            ->withTimestamps();
    }
}
