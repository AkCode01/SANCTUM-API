<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Post;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['posts'] = Post::all();
        return response()->json([
            'status'=>true,
            'message'=>'All Posts',
            'data'=>$data,
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateUser = Validator::make(
            $request->all(),
            [
                'title'=>'required',
                'description'=>'required',
                'image'=>'required|mimes:png,jpg',
            ]
        );
        if($validateUser->fails()){
            return response()->json([
                'status'=>false,
                'message'=>'Validation Error',
                'errors'=>$validateUser->errors()->all()
            ],401);
        }
        $img = $request->image;
        $ext = $img ->getClientOriginalExtension();
        $imageName = time() . '.' . $ext;
        $img ->move(public_path() . '/uploads',$imageName);
        $post = Post::create([
            'title'=>$request->title,
            'description'=>$request->description,
            'image'=>$imageName,
        ]);
        return response()->json([
            'status'=>true,
            'message'=>'Post Created',
            'post'=>$post,
        ],200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data['post']=Post::select(
            'id','title','description','image'
        )->where(['id'=>$id])->get();
        return response()->json([
            'status'=>true,
            'message'=>'Your Single Post',
            'data'=>$data,
        ],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validateUser = Validator::make(
            $request->all(),
            [
                'title'=>'required',
                'description'=>'required',
                'image'=>'mimes:png,jpg',
            ]
        );
        if($validateUser->fails()){
            return response()->json([
                'status'=>false,
                'message'=>'Validation Error',
                'errors'=>$validateUser->errors()->all()
            ],401);
        }
        $postImage = Post::select('id','image')->where(['id'=>$id])->get();
        // return $postImage[0]->image;
        if($request->image != '')
        {
            $path = public_path() . '/uploads';
            if( $postImage[0]->image !='' && $postImage[0]->image != null)
            {
                $old_file = $path . '/' . $postImage[0]->image;
                if(file_exists($old_file))
                {
                unlink($old_file);
                } 
            }

            $img = $request->image;
            $ext = $img ->getClientOriginalExtension();
            $imageName = time() . '.' . $ext;
            $img ->move(public_path() . '/uploads',$imageName);
        }else{
            $imageName = $postImage[0] ->image;
        }
        $post = Post::where(['id'=>$id])-> update([
            'title'=>$request->title,
            'description'=>$request->description,
            'image'=>$imageName,
        ]);
        return response()->json([
            'status'=>true,
            'message'=>'Post Updated successfully',
            'post'=>$post,
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $imagePath = Post::select('image')->where('id',$id)->get();
        $filePath = public_path()."/uploads" . $imagePath[0]['image'];
        unlink($filePath);
        $post = Post::where('id',$id)->delete();
        return response()->json([
        'status'=>true,
        'message'=>'Post removed',
        'post'=>$post,
        ],200);
    }
}