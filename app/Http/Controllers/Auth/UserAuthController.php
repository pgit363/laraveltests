<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Mail;

class UserAuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);

        $data['password'] = bcrypt($request->password);

        $user = User::create($data);

        return response([ 'user' => $user]);
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
          ]);
  
         $otp =  random_int(100000, 999999);

         $data = [
            'subject' => 'otp',
            'email' => $request->email,
            'content' => 'Hello your otp is '.$otp
          ];
  
          User::where('email', $request->email)->update(array('otp' => $otp));

          Mail::send('email-template', $data, function($message) use ($data) {
          $message->to($data['email'])
                ->subject($data['subject']); 
          $message->from('kamblepranav460@gmail.com','DoNotReply');
          });

          return response(['message' => 'Email successfully sent!']);
    }

    public function verifyOtp(Request $request)
    {
        $data = $request->validate([
            'email' => 'email|required',
            'otp' => 'required',
            
        ]);

        $user = User::where('email', $request->email)->where('otp', $request->otp)->first();
        
        if ($user) {
            User::where('email', $request->email)->update(array('otp' => null));
            $token = $user->createToken('API Token')->accessToken;
            return response(['user' => $user, 'token' => $token]);
        }
        return response(['user' => 'failed']);                    
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($data)) {
            return response(['error_message' => 'Incorrect Details. 
            Please try again']);
        }

        $token = auth()->user()->createToken('API Token')->accessToken;

        return response(['user' => auth()->user(), 'token' => $token]);
    }
}
