<?php
namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;

use  App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;


class AuthController extends Controller
{   

   
    public function register(Request $request)
    {
  
        $formFields = $request->validate([
            'first_name' => ['required', 'min:3'],
            'last_name' => ['required', 'min:3'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => 'required|min:6|confirmed'
        ]);
        
        // Hash Password
        $formFields['user_role'] = 'user';
        $formFields['password'] = bcrypt($formFields['password']);
        $user = User::create($formFields);
        event(new Registered($user));
        $token = $user->createToken('myapptoken')->plainTextToken;
        $response = [
            'message' => 'Registration successful. Please check your email for verification link.',
             'user'=> $user, 
             //'token' => $token   
             ];
        return response($response, 201); 

    }

    public function registerAdmin(Request $request)
    {
  
        $formFields = $request->validate([
            'first_name' => ['required', 'min:3'],
            'last_name' => ['required', 'min:3'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            //'user_role'=>'nullable',
            'password' => 'required|min:6|'
        ]);
         $formFields['user_role'] = 'admin';
        // Hash Password
        $formFields['password'] = bcrypt($formFields['password']);
        $user = User::create($formFields);

        event(new Registered($user));
        $token = $user->createToken('myapptoken')->plainTextToken;
       
        $response = [
            'message' => 'Registration successful. Please check your email for verification link.',
             'user'=> $user, 'token' => $token   ];

        return response($response, 201); 

    }



    public function verifyEmail($id, $hash)
    {
        // Abort if the user is not found or hash does not match
        $user = User::find($id);
        abort_if(!$user, 405);
        abort_if(!hash_equals($hash, sha1($user->getEmailForVerification())), 405);
  // Verify email if not already verified
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));        
            }
          //  return redirect()->to(env('FRONTEND_URL') . "/verify-email?status=success");
            return [   'message' => 'Email verified', 'status'=>true     ];
        
    }
        
    
        /********** ***********
         * Alternative way of verifying email
         * ************************* */

// public function verify(EmailVerificationRequest $request)
    // {
    //     if ($request->user()->hasVerifiedEmail()) {
    //         return [   'message' => 'Email already verified'     ];
    //     }

    //     if ($request->user()->markEmailAsVerified()) {
    //         event(new Verified($request->user()));
                // }

    //     return [      'message'=>'Email has been verified' ];
    // }
  

       public function login(Request $request) {
        $formFields = $request->validate([
            'email' => ['required', 'email'],
            // 'user_role' => ['required', 'email'],
            'password' => 'required|min:6'
        ]);

        // Hash Password
        $user = User::with('profile')->where('email', $formFields['email'])->first();

        if(!$user || !Hash::check($formFields['password'], $user->password)) {
            return response([
                'message' => 'Invalid login creds'
            ], 401);
        }

        if($user->email_verified_at == null) {
           $user->sendEmailVerificationNotification();
            return response([
                'message' => 'Please check your email for verification link', 'status'=>false
            ], 401);
        }
       
        $token = $user->createToken('myapptoken')->plainTextToken;
        //$response = [  'user'=> $formFields,   'token' => $token   ];
        $response= ['sign successful !!',
       // 'profile'=>$user->profile,
        'user'=> $user,
         'token'=> $token ];
        return response($response, 200); 
    }

      /********
       Admin Login endpoint
       */
    //   public function loginAdmin(Request $request) {
    //     $formFields = $request->validate([
    //         'email' => ['required', 'email'],
    //         'user_role' => ['required', 'email'],
    //         'password' => 'required|min:6'
    //     ]);

    //     // Hash Password
    //     $user = User::where('email', $formFields['email'])->first();

    //     if(!$user || !Hash::check($formFields['password'], $user->password)) {
    //         return response([
    //             'message' => 'Invalid login creds'
    //         ], 401);
    //     }
    //     if($user->user_role != 'admin') {
    //         abort(403, 'Unauthorized Action');
    //  }
    //     if($user->email_verified_at == null) {
    //        $user->sendEmailVerificationNotification();
    //         return response([
    //             'message' => 'Please check your email for verification link', 'status'=>false
    //         ], 401);
    //     }
       
    //     $token = $user->createToken('myapptoken')->plainTextToken;
    //     //$response = [  'user'=> $formFields,   'token' => $token   ];
    //     $response= ['sign successful !!','token' => $token ];
    //     return response($response, 201); 
    // }

        /*******
         * resend email verification
        //  ************/ 
        // public function resendLink(Request $request){
        //     $formFields = $request->validate([
        //         'email' => ['required', 'email'],
        //     ]);
        //     $user = User::where('email', $formFields['email'])->first();
        //     if($user){
        //         $user->sendEmailVerificationNotification();
        //         return response(['message'=>'Please check your email for verification link', 'status'=>true, 201]);
        //     }
        //     return response(['message'=>'Wrong email, pls confirm email', 'status'=>false, 401]);
        // }


    // Logout User
    public function logout(Request $request) {
       $request->user()->currentAccessToken()->delete();
       
        return [
            'message' => 'Logged out'
        ];
    }
  
    // Authenticate User
   
}

