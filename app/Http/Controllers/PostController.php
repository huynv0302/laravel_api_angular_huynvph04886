<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Post;
use App\Category;

class PostController extends Controller
{
    public function index(Request $request){
        $query = DB::table("posts")
                    ->join('categories', 'posts.cate_id', '=', 'categories.id')
                    ->select('posts.*', 'categories.name as cate_name', 'categories.slug as cate_slug');

        if($request->cateId != -1) {
            $query->where('posts.cate_id', $request->cateId);
        }

        $result = $query->paginate(12);
        return response()->json($result);
    }

    public function findById(Request $request){
		$result = DB::table("posts")
					->join('categories', 'posts.cate_id', '=', 'categories.id')
                    ->join('users', 'posts.user_id', '=', 'users.id')
					->select('posts.*', 'categories.name as cate_name', 'categories.slug as cate_slug', 'users.name as user_name')
                    ->where('posts.id', $request->id)
                    ->first();


    	return response()->json($result);
    }

    public function save(Request $request){
        $model = $request->id != null ? Post::find($request->id) : new Post();
        $model->fill($request->all());
        $model->user_id = 1;
        $path = false;  
        $result = [];
        if($request->hasFile('feature_images')){
            $request->feature_images->store('public/uploads');
            // $path = uniqid().'-'.$request->feature_images->getClientOriginalName();
            $model->feature_images = 'http://localhost:8000/storage/uploads/'.$request->feature_images->hashName();
        }
        elseif ($request->imageold != null) {
            $model->feature_images = $request->imageold;
        }
        else{
            $result['message'] = 'bạn chưa chọn ảnh';
            return response()->json($result);
        }
        $model->slug = str_slug($request->title, '-');
        $result = $model->save();
        if($result){
            $model['success'] = true;
        }
        return response()->json($model);
    }
    public function getAll(Request $req){
        $limit = 16;
        if($req->limit){
            $limit = $req->limit;
        }
        $type = 0;
        if($req->type){
            $type = $req->type;
        }

        $result = DB::table("posts")
                    ->join('categories', 'posts.cate_id', '=', 'categories.id')
                    ->join('users', 'posts.user_id', '=', 'users.id')
                    ->select('posts.*', 'categories.name as cate_name', 'categories.slug as cate_slug', 'users.name as user_name')
                    ->where('type',$type)
                    ->orderBy('id','desc')
                    ->paginate($limit);
        if($req->cate_id != null && $req->cate_id > 0){
            $result = DB::table("posts")
                        ->join('categories', 'posts.cate_id', '=', 'categories.id')
                        ->join('users', 'posts.user_id', '=', 'users.id')
                        ->select('posts.*', 'categories.name as cate_name', 'categories.slug as cate_slug', 'users.name as user_name')
                        ->where('type',$type)
                        ->where('cate_id',$req->cate_id)
                        ->orderBy('id','desc')
                        ->paginate($limit);
        }
        if(count($result) < 4 && $req->type){
            $result = DB::table("posts")
                        ->join('categories', 'posts.cate_id', '=', 'categories.id')
                        ->join('users', 'posts.user_id', '=', 'users.id')
                        ->select('posts.*', 'categories.name as cate_name', 'categories.slug as cate_slug', 'users.name as user_name')
                        ->orderBy('id','desc')
                        ->paginate($limit);
        }
        return response()->json($result);
    }

    public function getPostCate(Request $request){
    	if($request->cate_id){
            $limit = 16;
            if($request->limit){
                $limit = $request->limit;
            }
            $arr_id = [$request->cate_id];
            $cate_child = $this->parentCate($request->cate_id);
            foreach ($cate_child as $key => $value) {
               array_push($arr_id, $value->id);
            }
    		$model = Post::where('type',0)->whereIn('cate_id',$arr_id)->orderby('id','desc')->paginate($limit);

            for($i =0 ; $i < count($model); $i++){
                $model[$i]['category'] = $this->getCate($model[$i]->cate_id);
                $model[$i]['timetext'] = '25/07/2018';
            }
    		if(count($model) <= 0){
    			$model = ["success"=>1, "message" => "dữ liệu trống"];
    		}
    	}
    	else{
    		$model = ["success"=>0,"message" => "Chưa truyền id cate vô!"];
    	}

    	return response()->json($model);
    }

    public function getCate($cate_id){
        $model = Category::find($cate_id);
        return $model;
    }

    public function getUser(){
        
    }
    public function getHotPost(Request $req){
        $limit = 16;
        if($req->limit){
            $limit = $req->limit;
        }
        $result = DB::table("posts")
                    ->join('categories', 'posts.cate_id', '=', 'categories.id')
                    ->join('users', 'posts.user_id', '=', 'users.id')
                    ->select('posts.*', 'categories.name as cate_name', 'categories.slug as cate_slug', 'users.name as user_name')
                    ->where('type','=',1)
                    ->paginate($limit);
        return response()->json($result);
    }

    public function getPostSameCate(Request $req){
        $model = Post::find($req->post_id);
        $limit = 16;
        if($req->limit){
            $limit = $req->limit;
        }
        $arr = [];
        $result = DB::table("posts")
                    ->join('categories', 'posts.cate_id', '=', 'categories.id')
                    ->join('users', 'posts.user_id', '=', 'users.id')
                    ->select('posts.*', 'categories.name as cate_name', 'categories.slug as cate_slug', 'users.name as user_name')
                    ->where('cate_id','=',$model->cate_id)
                    ->paginate($limit);
        return response()->json($result);
    }

    public function search(Request $req){
        $limit = 16;
        if($req->limit){
            $limit = $req->limit;
        }
        if($req->keyword == ''){
            return '';
        }
        $keyword = "%$req->keyword%";
        $result = DB::table("posts")
                    ->join('categories', 'posts.cate_id', '=', 'categories.id')
                    ->join('users', 'posts.user_id', '=', 'users.id')
                    ->select('posts.*', 'categories.name as cate_name', 'categories.slug as cate_slug', 'users.name as user_name')
                    ->where('title','like',$keyword)
                    ->paginate($limit);
        if(count($result) <= 0){
            $a = ["error" => "Không tìm thấy từ khóa '".$req->keyword."'"];
            return response()->json($a);
        }
        return response()->json($result);
    }

    public function parentCate($cate_id){
        $model = Category::find($cate_id);
        $result = Category::where('parent_id',$model->id)->get();
        return $result;
    }

    public function delete(Request $req){
        $model = Post::find($req->id);
        $data = [];
        $success = false;
        if($model){
            $model->delete();
            $success = true;
        }
        $data['success'] = $success;
        return response()->json($data);
    }
}
