<?php

namespace App\Models;

use App\Models\Media;
use App\Helpers\Helper;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory , SoftDeletes;

    // var $fillable = [];
    var $guarded = ['id'];

    public function getMedia(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    // public function getThumbnail($id)
    // {
    //     return Media::where('product_id' , $id)->where('priority' , 1)->get() ;
    // }

    public function getComments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
