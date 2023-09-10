<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;

class MediaController extends Controller
{

    // public function upload_files($files, $type)
    // {
    //     $i = 0;
    //     foreach ($files as $file) {
    //         // $file = $request->file('image');

    //         //Display File Name
    //         // echo 'File Name: ' . $file->getClientOriginalName();
    //         // echo '<br>';

    //         // //Display File Extension
    //         // echo 'File Extension: ' . $file->getClientOriginalExtension();
    //         // echo '<br>';

    //         // //Display File Real Path
    //         // echo 'File Real Path: ' . $file->getRealPath();
    //         // echo '<br>';

    //         // //Display File Size
    //         // echo 'File Size: ' . $file->getSize();
    //         // echo '<br>';

    //         // //Display File Mime Type
    //         // echo 'File Mime Type: ' . $file->getMimeType();

    //         //Move Uploaded File
    //         $destinationPath = 'uploads/' . $type;
    //         $file->move($destinationPath, $file->getClientOriginalName());

    //         Media::create([
    //             'product_id' => $request->product_id,
    //             'priority' => $request->priority,
    //             'type' => $request->type,
    //             'path' => $request->path,
    //         ]);

    //         $i++;
    //     }
    //     return $i;
    // }
    public function create(Request $request)
    {
        dd($request->all());
        $input = $request->all();
        $res = 0;

        $files = $input['files'];
        $type = $input['type'];
        $product_id = $input['product_id'];
        // dd($files);
        foreach ($files as $file) {
            // dd($file);
            //Move Uploaded File
            $destinationPath = 'uploads/' . $type;
            // dd($file['detail']->getClientOriginalName());
            $file['detail']->move($destinationPath, rand(1000,9999) . $file['detail']->getClientOriginalName());

            Media::create([
                'product_id' => $product_id,
                'priority' => $file['priority'],
                'type' => $type,
                'path' => $destinationPath,
            ]);

            $res++;
        }

        return $res;

    }

    public function update(Request $request)
    {

        // $request->validate([
        //     'name' => 'required',
        //     'id' => 'nullable|numeric'
        // ]);

        $result = Media::where('id', $request->id)->update([
            'product_id' => $request->product_id,
            'priority' => $request->priority,
            'path' => $request->path,
        ]);

        return $result;
    }


    public function restore(Request $request)
    {

        // $request->validate([
        //     'id' => 'nullable|numeric'
        // ]);

        $result = Media::where('id', $request->id)->restore();

        return $result;
    }
    public function softdelete(Request $request)
    {

        // $request->validate([
        //     'id' => 'nullable|numeric'
        // ]);

        if (Media::where('id', $request->id)->exists()) {
            Media::where('id', $request->id)->delete();
            return 1;
        } else {
            return 0;
        }

    }

    public function forcedelete(Request $request)
    {

        // $request->validate([
        //     'id' => 'nullable|numeric'
        // ]);

        Media::where('id', $request->id)->restore();

        if (Media::where('id', $request->id)->exists()) {
            Media::where('id', $request->id)->forcedelete();
            return 1;
        } else {
            return 0;
        }

    }


    public function listMedias()
    {
        $Medias = Media::orderBy('id', 'DESC')->get();

        return $Medias;
    }
}