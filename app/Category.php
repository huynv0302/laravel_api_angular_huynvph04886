<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public $table = "categories";
    
    public function category()
    {
    	return $this->hasMany(Category::class, 'parent_id');
    }
}
