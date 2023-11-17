<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{ Model, SoftDeletes };

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $guarded = ['id','created_at','updated_at'];

    public function dealership()
    {
        return $this->belongsTo(Dealership::class);
    }

    public function device()
    {
        return $this->hasOne(DeviceInfo::class);
    }
}
