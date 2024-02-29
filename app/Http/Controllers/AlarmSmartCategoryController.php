<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\AlarmSmartCategory;
use Illuminate\Http\Request;

class AlarmSmartCategoryController extends Controller
{
    public function listAlarmSmartCategory()
    {
        $input = Request()->all();

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        // $alarm = AlarmSmartCategory::where("status", $input['status'])->get();
        $alarm = AlarmSmartCategory::orderBy('status', 'desc')->get();

        return $alarm;
    }
}
