<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dealership extends Model
{
    use HasFactory;

    protected $guarded = ['id','created_at','updated_at'];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class)->where('owner_type', 1)->whereNull('deleted_at');
    }

    public function devices()
    {
        return $this->hasManyThrough(DeviceInfo::class, Vehicle::class);
    }    
}
