<?php

namespace App\Http\Controllers\Front;

use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Cart;
use App\Models\Country;
use App\Models\Barangay;
use App\Models\DeliveryAddress;
use Auth;
use Validator;
use Session;
use Hash;

class UserController extends Controller
{
    public function loginRegister(){    
        $barangays = Barangay::all(); // Retrieve all barangay data
        return view('front.users.login_register', compact('barangays'));
    }

    public function userRegister(Request $request)
    {
        $data = $request->all();
    
            // dd($request->all());
            $validator = Validator::make($request->all(), [
                'firstname' => 'required|regex:/^[a-zA-Z\s]+$/|max:100',
                'lastname' => 'required|regex:/^[a-zA-Z\s]+$/|max:100',
                'middleinitial' => 'nullable|alpha|max:1', 
                'suffix' => 'nullable',
                'delivery_address'  => 'required',
                'delivery_barangay' => 'required',
                'mobile' => 'required|numeric|digits:11|unique:users',
                'email' => 'required|email|max:150|regex:/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/|unique:users',
                'password' => 'required|confirmed|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
                'accept' => 'required'
            ],
            [
                'delivery_address.required' => 'Delivery Address Details is Required',
                'delivery_barangay.required' => 'Barangay is Required',
                'accept.required' => 'Please accept our Terms & Conditions',
                'firstname.regex' => 'First Name should be in valid format.',
                'lastname.regex' => 'Last Name should be in valid format.',
                'email.regex' => 'Email should be in valid format.'
            ]);

            
    
            if ($validator->passes()) {
               // Concatenate first name, middle initial (if provided), last name, and suffix (if provided)
            $fullName = $data['firstname'];

            if(isset($data['middleinitial']) && !empty($data['middleinitial'])) {
                $fullName .= ' ' . $data['middleinitial'];
            }

            $fullName .= ' ' . $data['lastname'];

            if(isset($data['suffix']) && !empty($data['suffix'])) {
                $fullName .= ' ' . $data['suffix'];
            }

    
                // Register the User
                $user = new User;
                $user->name = $fullName;
                $user->address = $request->input('delivery_address');
                $user->city = 'Zamboanga City';
                $user->barangay = $request->input('delivery_barangay');
                $user->country = 'Philippines';
                $user->pincode = '7000';
                $user->mobile = $request->input('mobile');
                $user->email = $request->input('email');
                $user->password = bcrypt($request->input('password'));
                $user->status = 0;
                $user->save();

                // get user Id
                $users_id = $user->id;

                //Register Delivery Address
                $deliver = new DeliveryAddress;
                $deliver->user_id = $users_id;
                $deliver->name = $fullName;
                $deliver->address = $request->input('delivery_address');
                $deliver->city = 'Zamboanga City';
                $deliver->barangay = $request->input('delivery_barangay');
                $deliver->country = 'Philippines';
                $deliver->pincode = '7000';
                $deliver->mobile = $request->input('mobile');
                $deliver->status = 1;
                //Set default timezone to Manila
                date_default_timezone_set("Asia/Manila");
                $deliver->created_at = date("Y-m-d H:i:s");
                $deliver->updated_at = date("Y-m-d H:i:s");
                $deliver->save();


    
                // Send confirmation email
                $email = $request->input('email');
                $messageData = ['name' => $fullName, 'email' => $request->input('email'), 'code' => base64_encode($request->input('email'))];
                Mail::send('emails.confirmation', $messageData, function ($message) use ($email) {
                    $message->to($email)->subject('Confirm your WMSU TBI account');
                });
                
    
                // Redirect back user with success message
                $redirectTo = url('user/login-register');
                return response()->json(['type' => 'success', 'url' => $redirectTo, 'message' => 'Please confirm your email to activate your account!']);
            } else {
                return response()->json(['type' => 'error', 'errors' => $validator->messages()]);
            }
    }

    public function showBarangayTable()
    {
        $barangays = Barangay::all(); // Retrieve all barangay data

        // Pass the barangay data to the view
        return view('front.users.login_register')->with(compact('barangays'));

    }
    

    public function userAccount(Request $request){
        if($request->ajax()){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            $validator = Validator::make($request->all(), [
                    'name' => 'required|string|max:100',
                    'city' => 'required|string|max:100',
                    'barangay' => 'required|string|max:100',
                    'address' => 'required|string|max:100',
                    'country' => 'required|string|max:100',
                    'mobile' => 'required|numeric|digits:11',
                    'pincode' => 'required|digits:4',

                ]
            );

            if($validator->passes()){

                // Update User Details
                User::where('id',Auth::user()->id)->update(['name'=>$data['name'],'mobile'=>$data['mobile'],'city'=>$data['city'],'barangay'=>$data['barangay'],'country'=>$data['country'],'pincode'=>$data['pincode'],'address'=>$data['address']]);

                // Redirect back user with success message
                return response()->json(['type'=>'success','message'=>'Your contact/billing details successfully updated!']);

            }else{
                return response()->json(['type'=>'error','errors'=>$validator->messages()]);
            }

        }else{
            $zcbarangay = Barangay::all()->toArray();
            $countries = Country::where('status',1)->get()->toArray();
            return view('front.users.user_account')->with(compact('countries','zcbarangay'));
        }
    }

    public function userUpdatePassword(Request $request){
        if($request->ajax()){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/
            $validator = Validator::make($request->all(), [
                    'current_password' => 'required',
                    'new_password' => 'required|min:6',
                    'confirm_password' => 'required|min:6|same:new_password'

                ]
            );

            if($validator->passes()){

                $current_password = $data['current_password'];
                $checkPassword = User::where('id',Auth::user()->id)->first();
                if(Hash::check($current_password,$checkPassword->password)){

                    // Update User Current Password
                    $user = User::find(Auth::user()->id);
                    $user->password = bcrypt($data['new_password']);
                    $user->save();

                    // Redirect back user with success message
                return response()->json(['type'=>'success','message'=>'Account password successfully updated!']);

                }else{
                    // Redirect back user with error message
                    return response()->json(['type'=>'incorrect','message'=>'Your current password is incorrect!']);    
                }


                // Redirect back user with success message
                return response()->json(['type'=>'success','message'=>'Your contact/billing details successfully updated!']);

            }else{
                return response()->json(['type'=>'error','errors'=>$validator->messages()]);
            }

        }else{
            $countries = Country::where('status',1)->get()->toArray();
            return view('front.users.user_account')->with(compact('countries'));
        }
    }

    public function forgotPassword(Request $request){
        if($request->ajax()){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/

            $validator = Validator::make($request->all(), [
                    'email' => 'required|email|max:150|exists:users'
                ],
                [
                    'email.exists'=>'Email does not exists!'
                ]
            );

            if($validator->passes()){
                // Generate New Password
                $new_password = Str::random(16);

                // Update New Password
                User::where('email',$data['email'])->update(['password'=>bcrypt($new_password)]);

                // Get User Details
                $userDetails = User::where('email',$data['email'])->first()->toArray();

                // Send Email to User
                $email = $data['email'];
                $messageData = ['name'=>$userDetails['name'],'email'=>$email,'password'=>$new_password];
                Mail::send('emails.user_forgot_password',$messageData,function($message) use($email){
                    $message->to($email)->subject('New Password - WMSU TBI');
                });

                // Show Success Message
                return response()->json(['type'=>'success','message'=>'New Password sent to your registered email.']);

            }else{
                return response()->json(['type'=>'error','errors'=>$validator->messages()]);
            }

        }else{
            return view('front.users.forgot_password');    
        }
    }

    public function userLogin(Request $request){
        if($request->Ajax()){
            $data = $request->all();
            /*echo "<pre>"; print_r($data); die;*/

            $validator = Validator::make($request->all(), [
                'email' => 'required|email|max:150|exists:users',
                'password' => 'required|min:6'
            ]);

            if($validator->passes()){

                if(Auth::attempt(['email'=>$data['email'],'password'=>$data['password']])){

                    if(Auth::user()->status==0){
                        Auth::logout();
                        return response()->json(['type'=>'inactive','message'=>'Your account is not activated! Please confirm your account to activate your account.']);
                    }

                    // Update User Cart with user id
                    if(!empty(Session::get('session_id'))){
                        $user_id = Auth::user()->id;
                        $session_id = Session::get('session_id');
                        Cart::where('session_id',$session_id)->update(['user_id'=>$user_id]);
                    }


                    $redirectTo = url('cart');
                    return response()->json(['type'=>'success','url'=>$redirectTo]);
                }else{
                    return response()->json(['type'=>'incorrect','message'=>'Incorrect Email or Password!']);
                }

            }else{
                return response()->json(['type'=>'error','errors'=>$validator->messages()]);
            }

        }
    }

    public function userLogout(){
        Auth::logout();
        Session::flush();
        return redirect('/');
    }

    public function confirmAccount($code){
        $email = base64_decode($code);
        $userCount = User::where('email',$email)->count();
        if($userCount>0){
            $userDetails = User::where('email',$email)->first();
            if($userDetails->status==1){
                // Redirect the user to Login/Register Page with error message
                return redirect('user/login-register')->with('error_message','Your account is already activated. You can login now.');
            }else{
                User::where('email',$email)->update(['status'=>1]);

                // Send Welcome Email
                $messageData = ['name'=>$userDetails->name,'mobile'=>$userDetails->mobile,'email'=>$email];
                Mail::send('emails.register',$messageData,function($message)use($email){
                    $message->to($email)->subject('Welcome to WMSU TBI Shop');
                });

                // Redirect the user to Login/Register Page with success message
                return redirect('user/login-register')->with('success_message','Your account is activated. You can login now.');
            }
        }else{
            abort(404);
        }
    }


}
