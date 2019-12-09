<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function getIndex()
    {
       $posts = Post::paginate(5);

       return view('blog.index')->withPosts($posts);
    }

    public function getSingle($slug)
    {
        //fetch from database on slug
        $post = Post::where('slug', '=', $slug)->first();
        //return the view and pass in the post
        return view('blog.single')->with('post', $post);

    }
}
