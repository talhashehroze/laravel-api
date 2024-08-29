<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $data['posts'] = Post::all();
        return response()->json(
            [
                'status' => true,
                'message' => 'all posts data',
                'data'=>$data
            ],200
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
       
        $validatePost = Validator::make(
            $request->all(),
            [
                'title'=>'required',
                'description'=>'required',
                'image'=>'required|mimes:png,jpg,jpeg,gif'
            ]
        );

        if($validatePost->fails())
        {
            return response()->JSON([
                'status'=>false,
                'message'=> 'Validation Error',
                'error' => $validatePost->errors()->all()
            ],401);
        }

        $image = $request->image;
        $ext = $image->getClientOrignalExtension();
        $imageName = time(). ' .'. $ext;
        $image->move(public_path().'/uploads', $imageName);

        $post = Post::create([
            'title'=>$request->title,
            'description'=>$request->description,
            'image'=>$imageName
        ]);

        return response()->JSON([
            'status'=>true,
            'message'=> 'Post created successfully',
            'post' => $post
        ],200);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $data['post'] = Post::select(
            'id',
            'title',
            'description',
            'image'
        )->where(['id'=> $id])->get();

        return response()->JSON([
            'status'=>true,
            'message'=> 'Post get successfully',
            'post' => $data
        ],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatePost = Validator::make(
            $request->all(),
            [
                'title'=>'required',
                'description'=>'required',
                'image'=>'required|mimes:png,jpg,jpeg,gif'
            ]
        );

        if($validatePost->fails())
        {
            return response()->JSON([
                'status'=>false,
                'message'=> 'Validation Error',
                'error' => $validatePost->errors()->all()
            ],401);
        }

        $postImage = Post::select('id','image')->where('id',$id)->get();
        if($request->image != '')
        {
                $path = public_path().'/uploads';
                if($postImage[0]->image != '' && $postImage[0]->image!=null)
                {
                    $oldfile = $path . $postImage[0]->image;
                    if(file_exists($oldfile))
                    {
                        unlink($oldfile);
                    }
                }
            $image = $request->image;
            $ext = $image->getClientOrignalExtension();
            $imageName = time(). ' .'. $ext;
            $image->move(public_path().'/uploads', $imageName);
        }else{
            $imageName = $post->image;
        }
        

        $post = Post::where(['id'=>$id])->update([
            'title'=>$request->title,
            'description'=>$request->description,
            'image'=>$imageName
        ]);

        return response()->JSON([
            'status'=>true,
            'message'=> 'Post updated successfully',
            'post' => $post
        ],200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $imagePath = Post::select('image')->where('id',$id)->get();
        $post = Post::where(['id'=>$id])->delete();
        $filePath = public_path().'/uploads/' . $imagePath[0]['image'];

        unlink($filePath);
        return response()->json([
            'status'=>true,
             'message'=> 'Post deleted successfully',

        ]);

    }
}
