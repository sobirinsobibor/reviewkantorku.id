<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndustryOffice extends Model
{
    protected $table = 'industry_office';

    protected $fillable = [
        'office_id',
        'industry_id',
    ];

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }
}
