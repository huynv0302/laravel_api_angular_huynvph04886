<?php

use Illuminate\Database\Seeder;

class PostTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $posts = [];
        $faker = Faker\Factory::create();
        for ($i=0; $i < 100; $i++) { 
        	array_push($posts, [
    			'title' => $faker->name,
    			'user_id' => 1,
    			'cate_id' => rand(7, 17),
    			'slug' => str_slug($faker->name),
    			'content' => $faker->realText(200, 2),
    			'feature_images' => 'http://localhost:8000/uploads/posts/img1.jpg',
    			'short_desc' => $faker->realText(200, 2)
        	]);
        }
        DB::table('posts')->insert($posts);
    }
}
