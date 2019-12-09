<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PagesController extends Controller
{
    public function getIndex(){
        //$posts= Post::orderBy('created_at', 'desc')->limit(2)->get();
        $posts= Post::orderBy('created_at', 'desc')->paginate(4);

        return view('pages.welcome', compact('posts'));
    }
    public function getAbout(){
        $first= 'Hamid';
        $last='Niromand';
        $fullname= $first.''.$last;
        $email='alex@gmail.com';
        $data=[];
        $data['fullname']= $fullname;
        $data['email']= $email;

        return view('pages.about')->with('data', $data);
    }
    public function getContact(){
        return view('pages.contact');
    }
    public function postContact(Request $request){
        $this->validate($request, [
            'email' => 'required|email',
            'message' => 'min:10',
            'subject' => 'min:3'
        ]);
        $data = [
            'email' => $request->email,
            'subject' => $request->subject,
            'bodyMessage' => $request->message,
        ];
        Mail::send('emails.contact', $data, function($message) use ($data){
            $message->from($data['email']);
            $message->to('hello-abdff5@inbox.mailtrap.io');
            $message->subject($data['subject']);
        });

        Session::flash('success', 'your email was sent.');
        return redirect()->url('/');

        //return view('pages.contact');
    }
}
