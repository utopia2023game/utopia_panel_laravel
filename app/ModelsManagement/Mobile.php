<?php

namespace App\ModelsManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mobile extends Model
{
    use HasFactory , SoftDeletes;
}
