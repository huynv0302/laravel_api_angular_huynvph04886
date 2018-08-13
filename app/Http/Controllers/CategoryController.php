<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;

class CategoryController extends Controller
{
    public function list(){
		$cates = Category::paginate(10);
		return response()->json($cates);
	}
	public function index(){
		$cates = Category::where('parent_id', 0)->get();
		$array = $cates->toArray();
		$i = 0;
		foreach ($cates as $cate) {
			if ($cate->category) {
				$array[$i++]['category'] = $cate->category()->get()->toArray();
			}
		}
		return response()->json($cates->toArray());
	}

	public function parent($id){
		$result = Category::where('parent_id',$id)->get();
		return $result;
	}
	
	public function isParent($idcate){
		$result = Category::find($idcate);
		$cate_parent = Category::where('id',$result->parent_id)->get();
		if(count($cate_parent) > 0){
			return true;
		}
		return false;
	}

	public function getOneCate(Request $req){
		$idcate = $req->cate_id;
		$cates = Category::find($idcate);
		$cate_parent = [];
		$cate_child = [];
		$cate_length = $this->parent($cates->id);
		if(count($cate_length) > 0){
			$cate_parent = $this->parent($cates->id);
			$cates["cate_child"] = $cate_parent;
			$cate_child = $cate_parent;
		}

		return response()->json($cates);
	}
}
