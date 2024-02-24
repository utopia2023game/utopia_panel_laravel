<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\AlarmCategory;
use Illuminate\Http\Request;

class AlarmCategoryController extends Controller
{
    public function listAlarmCategory()
    {
        $input = Request()->all();

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        // $alarm = AlarmCategory::where("status", $input['status'])->get();
        $alarm = AlarmCategory::orderBy('status', 'desc')->get();

        return $alarm;
    }
}
