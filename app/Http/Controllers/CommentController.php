<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{


    public function create()
    {
        $input = Request()->all();

        Helper::DBConnection('utopia_store_' . $input['idb']);

        Comment::create([
            'product_id' => $input['product_id'],
            'user_id' => $input['user_id'],
            'name' => $input['name'],
            'text' => $input['text'],
            'rate' => $input['rate'],
        ]);

        return 1;
    }

    public function update()
    {
        $input = Request()->all();

        Helper::DBConnection('utopia_store_' . $input['idb']);

        $comment = Comment::where('id', $input['id'])->update([
            'status' => $input['status'],
            'name' => $input['name'],
            'response' => $input['response'],
            'text' => $input['text'],
        ]);

        return $comment;
    }

    public function list()
    {
        $input = Request()->all();

        Helper::DBConnection('utopia_store_' . $input['idb']);

        if ($input['status'] == 0) {
            $comments = Comment::where('status', '<>', 4)->orderBy('created_at', 'desc')->get();
        } else {
            $comments = Comment::where('status', $input['status'])->orderBy('created_at', 'desc')->get();
        }

        return $comments;
    }

    public function confirmComment(Request $request)
    {
        $input = $request->all();

        $ids = json_decode($input['id']);

        Helper::DBConnection('utopia_store_' . $input['idb']);

        $conut = 0;
        for ($i = 0; $i < count($ids); $i++) {
            $comment = Comment::find($ids[$i]);

            $comment = $comment->update([
                'status' => $input['status'],
                'submit_user_id' => $input['submit_user_id'],
            ]);

            $comment > 0 ? $conut = $conut + 1 : null;
        }


        return $conut;
    }

    public function softdelete()
    {
        $input = Request()->all();

        $ids = json_decode($input['id']);

        $conut = 0;
        for ($i = 0; $i < count($ids); $i++) {
            $comment = Comment::where('id', $ids[$i])->restore();

            if (Comment::where('id', $ids[$i])->exists()) {
                $comment = Comment::where('id', $ids[$i])->delete();
                $comment = $comment > 0 ? $conut = $conut + 1 : null;
            }

        }
        return $conut;

    }

    public function forcedelete()
    {
        $input = Request()->all();

        Helper::DBConnection('utopia_store_' . $input['idb']);

        $ids = json_decode($input['id']);

        $conut = 0;
        for ($i = 0; $i < count($ids); $i++) {
            $comment = Comment::where('id', $ids[$i])->restore();

            if (Comment::where('id', $ids[$i])->exists()) {
                $comment = Comment::where('id', $ids[$i])->forcedelete();
                $comment = $comment > 0 ? $conut = $conut + 1 : null;
            }

        }
        return $conut;
    }
    public function restore()
    {

        $input = Request()->all();

        Helper::DBConnection('utopia_store_' . $input['idb']);

        $result = Comment::where('id', $input['id'])->restore();

        return $result;
    }

    public function setLikeComment()
    {
        $input = Request()->all();

        Helper::DBConnection('utopia_store_' . $input['idb']);

        $comment = Comment::find($input['id']);

        $likeDislike = $input['like'] > 0 ? 'like' : 'dislike';
        $comment = $comment->update([
            $likeDislike => $input['like'] > 0 ? ($comment->like) + 1 : ($comment->dislike) + 1
        ]);

        return $comment;
    }

    public function setResponseComment()
    {
        $input = Request()->all();

        Helper::DBConnection('utopia_store_' . $input['idb']);

        $product = Comment::find($input['id']);

        $product = $product->update([
            'response' => $input['response']
        ]);

        return $product;
    }
}