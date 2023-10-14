<?php

namespace App\Http\Controllers;

use App\Models\OrderStatus;
use App\Helpers\Helper;
use Illuminate\Http\Request;

class OrderStatusController extends Controller
{
    public function listOrdersStatus()
    {
        $input = Request()->all();

        Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $input['idb']);

        $Order = OrderStatus::all();

        return $Order;
    }

}
