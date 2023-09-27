<?php

namespace App\Http\Controllers;

use Exception;
use App\Helpers\Helper;
use App\Models\Delivery;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function DeliveryCreate()
    {
        $input = Request()->all();

        $idb = $input['idb'];

        Helper::DBConnection('utopia_store_' . $idb);

        $Delivery = Delivery::all();


        $a['name'] = $input['name'];
        $a['description'] = $input['description'];
        $a['price'] = $input['price'];
        $a['delay_day_start'] = $input['delay_day_start'];
        $a['delay_day_until'] = $input['delay_day_until'];
        $a['delay_day_from'] = $input['delay_day_from'];
        $a['delay_time_from'] = $input['delay_time_from'];
        $a['delay_time_until'] = $input['delay_time_until'];

        if (count($Delivery) == 0) {
            $a['priority'] = 1;
        } else {
            $input['priority'] == 1 ? $a['priority'] = 1 : null;

            $input['priority'] == 1 ? $this->clearOtherPriority($Delivery) : null;
        }


        try {
            Delivery::create($a);
        } catch (Exception $e) {
            // return ($e->getMessage());
            return 0;
        }



        return 1;
    }

    public function clearOtherPriority($Delivery)
    {
        for ($i = 0; $i < count($Delivery); $i++) {
            Delivery::where('id', $Delivery[$i]['id'])->update([
                'priority' => 0
            ]);
        }
    }
    public function updateDelivery(Request $request)
    {
        $input = $request->all();

        $idb = $input['idb'];
        $Delivery_id = $input['id'];

        Helper::DBConnection('utopia_store_' . $idb);

        $Delivery = Delivery::where('id', $Delivery_id)->first();

        if (Delivery::where('id', $Delivery_id)->exists()) {
            $a['name'] = $input['name'];
            $a['description'] = $input['description'];
            $a['price'] = $input['price'];
            $a['delay_day_start'] = $input['delay_day_start'];
            $a['delay_day_until'] = $input['delay_day_until'];
            $a['delay_day_from'] = $input['delay_day_from'];
            $a['delay_time_from'] = $input['delay_time_from'];
            $a['delay_time_until'] = $input['delay_time_until'];
            try {
                $Delivery = $Delivery->update($a);
            } catch (Exception $e) {
                // return ($e->getMessage());
                // return 0;
            }
        } else {
            return 0;
        }
        return $Delivery;
    }

    public function listDeliverys()
    {
        $input = Request()->all();

        Helper::DBConnection('utopia_store_' . $input['idb']);

        $Delivery = Delivery::orderBy('priority', 'desc')->get();

        return $Delivery;
    }

    public function dataDeliveryByPriority()
    {
        $input = Request()->all();

        Helper::DBConnection('utopia_store_' . $input['idb']);

        $Delivery = Delivery::where('priority', 1)->first();

        return $Delivery;
    }

    public function softdelete()
    {
        $input = Request()->all();

        // dd($input);
        Helper::DBConnection('utopia_store_' . $input['idb']);

        $Delivery = Delivery::all();

        if (Delivery::where('id', $input['id'])->exists()) {

            $this->clearOtherPriority($Delivery);
            Delivery::where('id', $input['id'])->update(['priority' => 0]);
            Delivery::where('id', $input['id'])->delete();

            $Delivery = Delivery::all();
            if (count($Delivery) > 0) {
                Delivery::orderBy('created_at', 'desc')->first()->update(['priority' => 1]);
            }



            return 1;
        } else {
            return 0;
        }

    }

    public function forcedelete()
    {
        $input = Request()->all();

        Helper::DBConnection('utopia_store_' . $input['idb']);

        Delivery::where('id', $input['id'])->restore();

        if (Delivery::where('id', $input['id'])->exists()) {
            Delivery::where('id', $input['id'])->forcedelete();
            return 1;
        } else {
            return 0;
        }

    }
    public function restore(Request $request)
    {

        $input = Request()->all();

        Helper::DBConnection('utopia_store_' . $input['idb']);

        $result = Delivery::where('id', $input['id'])->restore();

        return $result;
    }

    public function setPriorityDelivery(Request $request)
    {
        $input = $request->all();

        Helper::DBConnection('utopia_store_' . $input['idb']);

        $Delivery = Delivery::all();
        $counter = 0;
        if (count($Delivery) > 0) {
            for ($i = 0; $i < count($Delivery); $i++) {
                $priority = 0;
                // echo 'Delivery id => ' . $Delivery[$i]['id'] . '    id => ' . $input['id'] . "\n";
                if ($Delivery[$i]['id'] == $input['id']) {
                    $priority = 1;
                }
                Delivery::where('id', $Delivery[$i]['id'])->update([
                    'priority' => $priority
                ]);
                $counter++;
            }
        } else {
            return 0;
        }
        return $counter == count($Delivery) ? 1 : 0;
    }

    public function confirmDelivery(Request $request)
    {
        $input = $request->all();

        $ids = json_decode($input['id']);

        Helper::DBConnection('utopia_store_' . $input['idb']);

        // return $ids;

        $conut = 0;
        for ($i = 0; $i < count($ids); $i++) {
            $Delivery = Delivery::where('id', $ids[$i])->first();

            if (Delivery::where('id', $ids[$i])->exists()) {
                $Delivery = $Delivery->update([
                    'status' => $input['status'],
                ]);
            }

            $Delivery > 0 ? $conut = $conut + 1 : null;
        }


        return $conut;
    }
}