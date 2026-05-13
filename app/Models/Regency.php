<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Regency extends Model
{
    protected $table = 'regencies';

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'province_id',
        'name',
        'is_active'
    ];

    /**
     * Regency belongs to province
     */
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }

    /**
     * Regency has many offices
     */
    public function offices()
    {
        return $this->hasMany(Office::class, 'regency_id', 'id');
    }
}
