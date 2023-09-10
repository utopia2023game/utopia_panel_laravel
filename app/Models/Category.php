<?php

namespace App\Models;

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
            return $this->parent->getParentsNames(). "   /   " . $this->name;
        } else {
            return $this->name;
        }
    }
    
    public function getParentsNamesWithoutSpace() {
        if($this->parent) {
            return $this->parent->getParentsNamesWithoutSpace(). "/" . $this->name;
        } else {
            return $this->name;
        }
    }
    
    public function getParentsIds() {
        if($this->parent) {
            return $this->parent->getParentsIds(). ',' . $this->id;
        } else {
            return $this->id;
        }
    }


    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
