<?php

namespace App\Http\Controllers\Front;

use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CmsPage;
use Validator;

class CmsController extends Controller
{
    public function cmsPage(){
        $currentRoute = url()->current();
        $currentRoute = str_replace("http://127.0.0.1:8000/","",$currentRoute);
        $cmsRoutes = CmsPage::select('url')->where('status',1)->get()->pluck('url')->toArray();
        if(in_array($currentRoute,$cmsRoutes)){
            $cmsPageDetails = CmsPage::where('url',$currentRoute)->first()->toArray();
            $meta_title = $cmsPageDetails['meta_title'];
            $meta_keywords = $cmsPageDetails['meta_keywords'];
            $meta_description = $cmsPageDetails['meta_description'];
            return view('front.pages.cms_page')->with(compact('cmsPageDetails','meta_title','meta_description','meta_keywords'));
        }else{
            abort(404);
        }
    }

    public function contact(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/

            $rules = [
                "name" => "required|string|max:100",
                "email" => "required|email|max:150",
                "subject" => "required|max:200",
                "message" => "required",
            ];

            $customMessages = [
                'name.required' => 'Name is required',
                'email.required' => 'Email is required',
                'email.email' => 'Valid Email is required',
                'subject.required' => 'Subject is required',
                'message.required' => 'Message is required',
            ];

            $validator = Validator::make($data,$rules,$customMessages);
            if($validator->fails()){
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Send User query to Admin
            $email = "admin1000@yopmail.com";
            $messageData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'subject' => $data['subject'],
                'comment' => $data['message'],
            ];

            Mail::send('emails.enquiry',$messageData,function($message)use($email){
                $message->to($email)->subject("Enquiry from WMSU TBI Shop");
            });

            $message = "Thanks for your query. We will get back to you soon.";
            return redirect()->back()->with('success_message',$message);

        }
        return view('front.pages.contact');
    }
}
