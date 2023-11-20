<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{ Model, SoftDeletes };

class Permission extends Model
{
    use HasFactory;
    use HasFactory, SoftDeletes;

    protected $guarded = ['id','created_at','updated_at'];
}
