<?php

namespace App\Http\Controllers;

use Exception;
use App\Helpers\Helper;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function AddressCreate()
    {
        $input = Request()->all();

        $idb = $input['idb'];

        Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $idb);

        $a['customer_id'] = $input['customer_id'];
        $a['receiver_name'] = $input['receiver_name'];
        $a['receiver_mobile'] = $input['receiver_mobile'];
        $a['receiver_post_code'] = $input['receiver_post_code'];
        $a['receiver_address'] = $input['receiver_address'];
        $a['priority'] = 1;

        $this->clearOtherPriority($input['customer_id']);

        try {
            Address::create($a);
        } catch (Exception $e) {
            // return ($e->getAddress());
            return 0;
        }



        return 1;
    }

    public function clearOtherPriority($customerId)
    {
        $Address = Address::where('customer_id', $customerId)->get();

        if (Address::where('customer_id', $customerId)->exists()) {

            for ($i = 0; $i < count($Address); $i++) {
                Address::where('customer_id', $customerId)->where('id', $Address[$i]['id'])->update([
                    'priority' => 0
                ]);
            }

        }
    }
    public function updateAddress(Request $request)
    {
        $input = $request->all();

        $idb = $input['idb'];
        $Address_id = $input['id'];

        Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $idb);

        $Address = Address::where('id', $Address_id)->first();

        if (Address::where('id', $Address_id)->exists()) {
            $a['customer_id'] = $input['customer_id'];
            $a['receiver_name'] = $input['receiver_name'];
            $a['receiver_mobile'] = $input['receiver_mobile'];
            $a['receiver_post_code'] = $input['receiver_post_code'];
            $a['receiver_address'] = $input['receiver_address'];
            try {
                $Address = $Address->update($a);
            } catch (Exception $e) {
                return ($e->getMessage());
                // return 0;
            }
        } else {
            return 0;
        }
        return $Address;
    }

    public function listAddresss()
    {
        $input = Request()->all();

        Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $input['idb']);

        $Address = Address::where('customer_id', $input['customer_id'])->orderBy('created_at', 'desc')->get();

        return $Address;
    }

    public function dataAddressByPriority()
    {
        $input = Request()->all();

        Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $input['idb']);

        $Address = Address::where('customer_id', $input['customer_id'])->where('priority', 1)->first();

        return $Address;
    }

    public function softdelete()
    {
        $input = Request()->all();

        // dd($input);
        Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $input['idb']);

        if (Address::where('id', $input['id'])->exists()) {

            $this->clearOtherPriority($input['customer_id']);
            Address::where('id', $input['id'])->update(['priority' => 0]);
            Address::where('id', $input['id'])->delete();

            if (Address::where('customer_id', $input['customer_id'])->exists()) {
                Address::where('customer_id', $input['customer_id'])->orderBy('created_at', 'desc')->first()->update(['priority' => 1]);
            }



            return 1;
        } else {
            return 0;
        }

    }

    public function forcedelete()
    {
        $input = Request()->all();

        Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $input['idb']);

        Address::where('id', $input['id'])->restore();

        if (Address::where('id', $input['id'])->exists()) {
            Address::where('id', $input['id'])->forcedelete();
            return 1;
        } else {
            return 0;
        }

    }
    public function restore(Request $request)
    {

        $input = Request()->all();

        Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $input['idb']);

        $result = Address::where('id', $input['id'])->restore();

        return $result;
    }

    public function setPriorityAddress(Request $request)
    {
        $input = $request->all();

        Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $input['idb']);

        $Address = Address::where('customer_id', $input['customer_id'])->get();

        $counter = 0;
        if (Address::where('customer_id', $input['customer_id'])->exists()) {
            for ($i = 0; $i < count($Address); $i++) {
                $priority = 0;
                // echo 'address id => ' . $Address[$i]['id'] . '    id => ' . $input['id'] . "\n";
                if ($Address[$i]['id'] == $input['id']) {
                    $priority = 1;
                }
                Address::where('customer_id', $input['customer_id'])->where('id', $Address[$i]['id'])->update([
                    'priority' => $priority
                ]);
                $counter++;
            }

        } else {
            return 0;
        }
        return $counter == count($Address) ? 1 : 0;
    }

    public function confirmAddress(Request $request)
    {
        $input = $request->all();

        $ids = json_decode($input['id']);

        Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $input['idb']);

        // return $ids;

        $conut = 0;
        for ($i = 0; $i < count($ids); $i++) {
            $Address = Address::where('id', $ids[$i])->first();

            if (Address::where('id', $ids[$i])->exists()) {
                $Address = $Address->update([
                    'status' => $input['status'],
                    'submit_user_id' => $input['submit_user_id'],
                ]);
            }

            $Address > 0 ? $conut = $conut + 1 : null;
        }


        return $conut;
    }
}