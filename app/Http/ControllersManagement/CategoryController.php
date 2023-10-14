<?php

namespace App\Http\ControllersManagement;

use Illuminate\Http\Request;
use App\ModelsManagement\Category;

class CategoryController extends Controller
{
    public function getListCategory(){
        return Category::all();
    }
}
