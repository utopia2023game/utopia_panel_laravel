<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use Exception;

class MessageController extends Controller
{
    public function MessageCreate()
    {
        $input = Request()->all();

        $image_file = $input['image_file'];
        $idb = $input['idb'];
        
        $type = 'messages';
        // return $input;
        // dd($input);

        Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $idb);

        $a['submit_user_id'] = $input['submit_user_id'];
        $a['customer_id'] = $input['customer_id'];
        $a['status'] = 2;
        $a['title'] = $input['title'];
        $a['subject'] = $input['subject'];
        $a['text'] = $input['text'];


        if (Request()->hasFile('image_file')) {
            $image_file_name = rand(1000, 9999) . '_' . $image_file->getClientOriginalName();
            $destinationPath = 'uploads/' . $idb . '/' . $type . '/';
            $image_file->move($destinationPath, $image_file_name);

            $a['image_path'] = $destinationPath . $image_file_name;
        }

        try {
            Message::create($a);
        } catch (Exception $e) {
            // return ($e->getMessage());
            return 0;
        }
        return 1;
    }
    public function updateMessage(Request $request)
    {
        $input = $request->all();
        $image_file = $input['image_file'];
        $idb = $input['idb'];
        $message_id = $input['id'];
        $submit_user_id = $input['submit_user_id'];
        $type = 'messages';

        Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $idb);


        $message = Message::where('id', $message_id)->first();

        if (Message::where('id', $message_id)->exists()) {
            $a['customer_id'] = $input['customer_id'];
            $a['title'] = $input['title'];
            $a['subject'] = $input['subject'];
            $a['text'] = $input['text'];

            if ($request->hasFile('image_file')) {
                $image_path = $message['image_path'] == null ? '' : $message['image_path'];
                try {
                    if ($image_path != '' && file_exists(public_path($image_path))) {
                        unlink(public_path($image_path));
                    }
                } catch (Exception $e) {
                    // return 0;
                }

                $image_file_name = rand(1000, 9999) . '_' . $image_file->getClientOriginalName();
                $destinationPath = 'uploads/' . $idb . '/' . $type . '/';
                $image_file->move($destinationPath, $image_file_name);

                $a['image_path'] = $destinationPath . $image_file_name;
            }


            try {
                $message = $message->update($a);
            } catch (Exception $e) {
                return ($e->getMessage());
                // return 0;
            }
        } else {
            return 0;
        }
        return $message;
    }

    public function listMessages()
    {
        $input = Request()->all();

        Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $input['idb']);

        if ($input['status'] == 0) {
            $message = Message::where('status', '<>', 4)->orderBy('created_at', 'desc')->get();
        } else {
            $message = Message::where('status', $input['status'])->orderBy('created_at', 'desc')->get();
        }

        return $message;
    }

    public function softdelete()
    {
        $input = Request()->all();

        // dd($input);
        Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $input['idb']);

        if (Message::where('id', $input['id'])->exists()) {
            Message::where('id', $input['id'])->delete();
            return 1;
        } else {
            return 0;
        }

    }

    public function forcedelete()
    {
        $input = Request()->all();

        Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $input['idb']);

        Message::where('id', $input['id'])->restore();

        if (Message::where('id', $input['id'])->exists()) {
            Message::where('id', $input['id'])->forcedelete();
            return 1;
        } else {
            return 0;
        }

    }
    public function restore(Request $request)
    {

        $input = Request()->all();

        Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $input['idb']);

        $result = Message::where('id', $input['id'])->restore();

        return $result;
    }

    public function setVisitMessage(Request $request)
    {
        $input = $request->all();

        Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $input['idb']);

        $message = Message::where('id', $input['id'])->first();

        if (Message::where('id', $input['id'])->exists()) {
            $message = $message->update([
                'visit' => ($message->visit) + 1
            ]);
        } else {
            return 0;
        }
        return $message;
    }

    public function confirmMessage(Request $request)
    {
        $input = $request->all();

        $ids = json_decode($input['id']);

        Helper::DBConnection(env('SERVER_STATUS' , '') . 'utopia_store_' . $input['idb']);

        // return $ids;

        $conut = 0;
        for ($i = 0; $i < count($ids); $i++) {
            $message = Message::where('id', $ids[$i])->first();

            if (Message::where('id', $ids[$i])->exists()) {
                $message = $message->update([
                    'status' => $input['status'],
                    'submit_user_id' => $input['submit_user_id'],
                ]);
            }

            $message > 0 ? $conut = $conut + 1 : null;
        }


        return $conut;
    }
}