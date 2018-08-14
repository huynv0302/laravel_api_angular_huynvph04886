<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public $table = 'posts';

   	protected $fillable = ['title', 'user_id', 'cate_id', 'content', 'short_desc', 'keywords' ,'feature_images', 'view', 'status'];

   	public function user(){
   		return $this->belongsTo(User::class);
   	}

   	public function category(){
   		return $this->belongsTo(Category::class, 'cate_id');
   	}
}
