<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\Request;
use App\Helpers\Helper;

class SliderController extends Controller
{
    public function setImagesSlider()
    {
        $input = Request()->all();

        $file = $input['image'];
        $idb = $input['idb'];
        $user_id = $input['user_id'];
        $type = 'image';
        // return $input;
        // dd($input);

        Helper::DBConnection('utopia_store_' . $idb);

        $file_name = rand(1000, 9999) . '_' . $file['detail']->getClientOriginalName();
        $destinationPath = 'uploads/' . $idb . '/' . $type . '/';
        $file['detail']->move($destinationPath, $file_name);

        Slider::create([
            'user_id' => $user_id,
            'priority' => $file['priority'],
            'type' => $type,
            'path' => $destinationPath . $file_name,
        ]);

        return 1;

        // $result_number = 0;
        // foreach ($files as $file) {
        //     $file_name = rand(1000, 9999) . '_' . $file['detail']->getClientOriginalName();
        //     // $destinationPath = 'uploads/' . $type . '/' . $file_name;
        //     $destinationPath = 'uploads/'.$idb.'/' . $type . '/';
        //     $file['detail']->move($destinationPath, $file_name);

        //     Slider::create([
        //         'user_id' => $user_id,
        //         'priority' => $file['priority'],
        //         'type' => $type,
        //         'path' => $destinationPath . $file_name,
        //     ]);
        //     $result_number++;
        // }
        // return $result_number;
    }

    public function listSliders()
    {
        $input = Request()->all();

        Helper::DBConnection('utopia_store_' . $input['idb']);

        $sliders = Slider::orderBy('id', 'DESC')->get();

        return $sliders;
    }

    public function softdelete()
    {
        $input = Request()->all();
        
        // dd($input);
        Helper::DBConnection( 'utopia_store_' . $input['idb']);

        if (Slider::where('id', $input['id'])->exists()) {
            Slider::where('id', $input['id'])->delete();
            return 1;
        } else {
            return 0;
        }

    }

    public function forcedelete()
    {
        $input = Request()->all();
        
        Helper::DBConnection( 'utopia_store_' . $input['idb']);

        Slider::where('id', $input['id'])->restore();

        if (Slider::where('id', $input['id'])->exists()) {
            Slider::where('id', $input['id'])->forcedelete();
            return 1;
        } else {
            return 0;
        }

    }
    public function restore(Request $request)
    {

        $input = Request()->all();
        
        Helper::DBConnection( 'utopia_store_' . $input['idb']);

        $result = Slider::where('id', $input['id'])->restore();

        return $result;
    }

    
}