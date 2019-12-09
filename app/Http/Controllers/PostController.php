<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Tag;
use App\Category;
use App\Post;
use Session;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //create a variable and store all the blog posts in it from  the database
        $posts=Post::orderBy('id', 'desc')->paginate(4);

        //return a view and pass in it above variable
        return view('posts.index')->withPosts($posts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('posts.create', compact(['categories', 'tags']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //do validation of data
        $this->validate($request, array(
            'title' => 'required|max:255',
            'slug' => 'required|alpha_dash|min:5|max:255',
            'body' => 'required',
            'category_id' => 'required|integer',
        ));
        //store data in database
        $post= new Post;

        $post->title = $request->title;
        $post->slug = $request->slug;
        $post->body = $request->body;
        $post->category_id = $request->category_id;

        $post->save();
        $post->tags()->sync($request->tags, false);

        Session::flash('success' , 'The blog post was successfully saved.');
        // redirect to another page
        return redirect()->route('posts.show', $post->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id);
        return view('posts.show')->with('post', $post);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //find the post in the database and save as a var
        $post= Post::find($id);
        $categories = Category::all();
        //$categories = Category::pluck('name','id');
        // Category::all()->lists('name', 'id') ;)
        $cats = [];
        foreach($categories as $category)
        {
            $cats[$category->id] = $category->name;
        }
        $tags = Tag::pluck('name', 'id');
        //return the view and pass in  the var we previously created.
        return view('posts.edit', compact(['post', 'cats', 'tags']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //Validate the data
        $post = Post::find($id);
        if($request->input('slug') == $post->slug)
        {
            $this->validate($request, array(
                'title' => 'required| max:255',
                'body' => 'required',
                'category_id' => 'required|integer',
            ));

        }else{

            $this->validate($request, array(
                'title' => 'required| max:255',
                'slug' => 'required|alpha_dash|min:5|max:255|unique:posts,slug',
                'body' => 'required',
                'category_id' => 'required|integer',

            ));
        }

        //Save the data to the database
        $post = Post::find($id);

        $post->title = $request->input('title');
        $post->slug = $request->input('slug');
        $post->body = $request->input('body');
        $post->category_id = $request->input('category_id');

        $post->save();

        if (isset($request->tags)) {
            $post->tags()->sync($request->tags);
        } else {
            $post->tags()->sync(array());
        }
//        dd($post);
        //$post->tags()->sync($request->tags);
        //set flash data with success message
        Session::flash('success', 'This post was successfully saved.');

        //redirect with flash data to posts.show
        return redirect()->route('posts.show', $post->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);
        $post->tags()->detach();
        $post->delete();

        Session::flash('success', 'The post was successfully deleted.');
        return redirect()->route('posts.index');
    }
}
