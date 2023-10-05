<?php

namespace App\ModelsManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory , SoftDeletes;

    // var $fillable = ['name','parent_id','deleted_at'];
    var $guarded = ['id'];

    public function children(){
        
        $result = $this->hasMany(Category::Class,'parent_id')->with('children');
        // dd($result);
        return $result;
    }
    public function parent()
    {
        return $this->belongsTo(Category::Class, 'parent_id');
    }
    
    public function getParentsNames() {
        if($this->parent) {
            return $this->parent->getParentsNames(). " -> " . $this->name;
        } else {
            return $this->name;
        }
    }
}
