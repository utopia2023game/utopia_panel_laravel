<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Alarm;
use Illuminate\Http\Request;

class AlarmController extends Controller
{
    public function listAlarmWithStatus(Request $request)
    {
        $input = Request()->all();

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);

        $alarm = Alarm::where("status", $input['status'])->get();

        return $alarm;
    }
}
