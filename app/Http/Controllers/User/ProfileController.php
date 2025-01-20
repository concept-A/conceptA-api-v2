<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\Group;
use App\Models\Profile;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\BusinessRequest;
// for middleware in controller
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class ProfileController extends Controller
{

    /******* ******** REMEMBER TO RESOLVE MIDDLEWARE **********/

    public static function middleware(): array
    {
        return [
             new Middleware(middleware: 'admin', only: [ 'index','delete']),
        ];
    }

    // Show all profile
    public function index() {
       $allProfile = Profile::with('categories')->get();
       // $allProfile = Profile::all();
       if($allProfile->isEmpty()){
        return response()->json( ['message' => 'empty pls create profile', 'status'=>false], 404);
       }
        // $allProfile = Profile::all();
        return response()->json( ['Profiles.groups' => $allProfile, 'status'=>true], 200);
    }

     //Show single user profile
     public function show(Request $request ) {
        $profile = Profile::with('categories')->findOrFail($request->id);

          return response()->json(['Profile' => $profile,'status'=>true], 200);
      }
      
      //Show single user profile
      public function userProfile(Request $request ) {
        $user = User::with('profile.categories','groups')->findOrFail($request->id);
        // if($user->id != auth()->id()) {
        //     abort(403, 'Unauthorized Action',);
        // }
          if(empty( $user->profile)){
            // if(!$user->profile){
              return response()->json(
                  ['message' => "You don't have a profile, please create one",
                  'status'=>true],404);
          }
          $profile = $user->profile;
          return response()->json(['Profile' => $profile,'status'=>true], 200);
      }



    /*****************
     *   // Store Business profile Data
     *********************************/
  
    public function store(Request $request) {
        $category = Category::all();
        if ($category->isEmpty() ) {
            // if (!$category) {
            return response()->json(
                ['message' => "pls add categories, categories empty",
                'status'=>false ],401);
        }

        $user = User::find(auth()->id());
        if(!empty($user->profile) ){
            return response()->json(
                ['message' => "user profile already exist",
                'status'=>false ],401);
        }

       $request->validate([
            'business_name' => 'unique:profiles|required',
            'shop_address' => 'required',
           'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1048',
            'contact' => 'required',
            'subscription' => 'nullable',
            'group_id' =>'nullable',
            'category_id' => 'required|array', // Ensure category_id is an array
    '   category_id.*' => 'exists:categories,id', // Validate that each category ID exists
        ]);
        
           $businessprofile = new Profile();
           $businessprofile->business_name = $request->business_name;
           $businessprofile ->shop_address= $request->shop_address;
           $businessprofile->contact =  $request->contact;
           // $businessprofile->category_id =  $request->category_id;
           $businessprofile->image =  $request->image;
          // $businessprofile->group_id =  1;

    if($request->hasFile('image')) {
        $businessprofile->image = $request->file('image')->store('profiles', 'public');
    }

    $businessprofile->user_id = auth()->id();
    
   // $businessprofile->user_id = 4;
    $businessprofile->save(); 

  // Attach categories and group to the product
  if ($request->has('category_id')) {
    $businessprofile->categories()->sync($request->category_id);
}
if ($request->has('group_id')) {
    $businessprofile->groups()->sync($request->group_id);
}

        return response()->json([
            'message'=> 'profile created successfully!',
            'profile'=> $businessprofile,'status'=>true], 200);
    }


    
    /*****************
     *   //  Business joingroup Data
     *********************************/
  
     public function joinGroup(Request $request) {
        $category = Group::all();
        if ($category->isEmpty() ) {
            // if (!$category) {
            return response()->json(
                ['message' => "pls create group, group empty",
                'status'=>false ],401);
        }
        $user = User::with('profile')->findOrFail($request->id);
       $request->validate([
            'group_id' =>'required',
        ]);
        $businessprofile = $user->profile;
        if ($request->group_id) {
            $businessprofile->group_id->attach($request->group_id);
        }
       $businessprofile->save();

        return response()->json([
            'message'=> 'joined group successfully!','status'=>true], 200);
    }

public function update(Request $request)
{
    $profile = DB::table('profiles')->where('user_id', $request->id)->first();
    if (empty($profile)) {
        return response()->json([
            'message' => "You don't have a profile, please create one",
            'status' => true,
        ]);
    }
    if (auth()->user()->user_role != 'admin' && $profile->user_id != auth()->id()) {
        abort(403, 'Unauthorized Action');
    }
    $validatedData = $request->validate([
        'business_name' => 'nullable|string|max:255',
        'shop_address' => 'nullable|string|max:255',
        // 'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:1048',
        'contact' => 'nullable|string|max:255',
        'subscription' => 'nullable|boolean',
        'category_id' => 'nullable|array',
        'category_id.*' => 'exists:categories,id',
        'group_id' => 'nullable|array',
        'group_id.*' => 'exists:groups,id',
        'image' => 'nullable|max:1048'
    ]);

    $businessProfile = Profile::find($profile->id);

    if ($request->hasFile('image')) {
        if ($businessProfile->image ) {
            Storage::disk('public')->delete($businessProfile->image);
        }
        $validatedData['image'] = $request->file('image')->store('profiles', 'public');
    }

    $businessProfile->update($validatedData);

    // Attach categories and groups if provided
    if ($request->has('category_id')) {
        $businessProfile->categories()->sync($request->category_id);
    }
    if ($request->has('group_id')) {
        $businessProfile->groups()->sync($request->group_id);
    }

    // Return response
    return response()->json([
        'message' => 'Profile updated successfully!',
        'profile' => $businessProfile,
        'status' => true,
    ], 200);
}



//  Delete user and business profile
    public function delete(Request $request) {
        $user = User::with('profile')->find($request->id);
        if(!$user->profile){
        return response()->json([ 'message'=>'user has no profile!'],404);

        }
        if($user->profile->image && Storage::disk('public')->exists($user->profile->image)) {
            Storage::disk('public')->delete($user->profile->image);     
   }

//    $user->delete();
        $user->profile->delete();
        return response()->json([ 'message'=>'profile deleted successfully!'],200);

}

}
