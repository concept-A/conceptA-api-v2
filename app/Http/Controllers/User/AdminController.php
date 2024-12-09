<?php

namespace App\Http\Controllers\User;

// use App\Models\Role;
use App\Models\User;
// use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

// for middleware in controller
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;



class AdminController extends Controller
{

/******* ********
 * REMEMBER TO RESOLVE MIDDLEWARE
 * *******/
//     public function __construct()
//     {
//      $this->middleware('admin')->only([ 'index','show', 'makeAdmin','makeUser','store', 'delete',  ]);
   
//     }

public static function middleware(): array
{
    return [
       new Middleware(middleware: 'admin', only: [ 'index','show', 'makeAdmin','makeUser','store', 'delete', ]),
       // new Middleware(middleware: 'auth:sanctum', except: ['index', 'show']),
    ];
}

    
    public function index() {
        $allUser = User::with('profile','groups')->get();
    //   $allUser = User::all();
       return response()->json( ['User' => $allUser,'status'=>true], 200);
        
    }

    //Show single user
    public function show(Request $request ) {   
       $user = User::with('profile')->findOrFail($request->id);
    // $user = User::findOrFail($request->id);    
    return response()->json(['user'=>$user,'status'=>true],200);
    }



    public function store(Request $request)
    {
        $formFields = $request->validate([
            'first_name' => ['required', 'min:3'],
            'last_name' => ['required', 'min:3'],
           'user_role'=>'nullable',  
            'email' => ['required','email',Rule::unique('users', 'email')],
            'password' => 'required|min:6|confirmed',
        ]);
//REMEMBER TO VERIFY EMAIL AUTHOMATICALLY
// to authomaticall verify email it is essential to seed the data into db to protected the field

        // $formFields['email_verified_at'] = now();  
           // Hash Password
           $formFields['password'] = bcrypt($formFields['password']);
           $user = User::create($formFields);
           $user->sendEmailVerificationNotification();
         $token = $user->createToken('myapptoken')->plainTextToken;

        return response()->json(['data'=>$user,'token'=>$token,'message'=>'user Created '],201);
    }



    public function update(Request $request)
    {
        $request->validate([
            'first_name' => ['string', 'min:3'],
            'last_name' => ['string', 'min:3'],
            // 'user_role'=>'',
            // 'email' => ['required', 'email', Rule::unique('users', 'email')],
        ]);

        $user = User::find($request->id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->first_name =$request->first_name;
        $user->last_name =$request->last_name;
        // $user->user_role =$request->user_role;
        $user->save();

        return response()->json(['data'=>$user,'message'=>'user updated '],200);
    }


    public function makeAdmin(Request $request)
    {
       $user = User::find($request->id);
       if(!$user){
        return response(['message'=>'user not found', 'status'=>false], 404);
       }
       if($user->user_role != 'admin'){
        $user->user_role = 'admin';
        $user->save();
        return response(['message'=> $user->first_name,'is now an admin.','status'=>true], 200);  
       }
       return response(['message'=>'user role already admin', 'status'=>false], 401);

    }

    public function makeUser(Request $request)
    {
       $user = User::find($request->id);
       if(!$user){
        return response(['message'=>'user not found', 'status'=>false], 404);
       }

       if($user->user_role != 'user'){
        $user->user_role = 'user';
        $user->save();
        return response(['message' => $user->first_name,'is now a user.','status'=>true], 200);  
       }
      
       return response(['message'=>' role already user', 'status'=>false], 401);
    }

    // Delete user with profile
    public function delete(Request $request) {       
        // $user = User::with('profile')->find($request->id);
        $user = User::find($request->id);

        if(!$user){
            return response()->json(
                [ 'message' => "user do not exist",
                  'status'=>false ]);
        }
        //  if(!$user->profile->image) {
        //   if($user->profile->image && Storage::disk('public')->exists($user->profile->image)) {
        //     Storage::disk('public')->delete($user->profile->image);            
           
        //     $user->profile->delete();     
                
        //         }
        //      }
         $user->delete();
         return response()->json([ 'message'=>'User deleted successfully!','status'=>true],200);

}

}
