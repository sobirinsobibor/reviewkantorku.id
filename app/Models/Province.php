<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $table = 'provinces';

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'is_active'
    ];

    public function regencies()
    {
        return $this->hasMany(Regency::class, 'province_id', 'id');
    }

    /**
     * Province has many offices
     */
    public function offices()
    {
        return $this->hasMany(Office::class, 'province_id', 'id');
    }

}
