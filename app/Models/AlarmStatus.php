<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AlarmStatus extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

}
