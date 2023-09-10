<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryStore extends Model
{
    use HasFactory , SoftDeletes;

    // var $fillable = ['name','parent_id','deleted_at'];

    protected $table = 'categories';
    var $guarded = ['id'];

}
