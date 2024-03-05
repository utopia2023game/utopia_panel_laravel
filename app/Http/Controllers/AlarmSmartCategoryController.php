<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\AlarmSmartCategory;
use App\Models\DiscountStatus;
use Illuminate\Http\Request;

class AlarmSmartCategoryController extends Controller
{
    public function listAlarmSmartCategory()
    {
        $input = Request()->all();

        Helper::DBConnection(env('SERVER_STATUS', '') . 'utopia_store_' . $input['idb']);
        
        $alarm = AlarmSmartCategory::orderBy('status', 'desc')->get();

        for ($i = 0; $i < count($alarm); $i++) {
            $DiscountStatus = DiscountStatus::get();
            $alarm[$i]['discount_status_name'] = $DiscountStatus->where('id', $alarm[$i]['discount_status_id'])->first()->name_fa;
        }

        return $alarm;
    }
}
