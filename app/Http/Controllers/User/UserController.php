<?php

namespace App\Http\Controllers\User;

// use App\Models\Role;
use App\Models\User;
// use App\Models\Department;
use App\Models\Group;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// for middleware in controller
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;


class UserController extends Controller
{

/******* ********
 * REMEMBER TO RESOLVE MIDDLEWARE
 * *******/
public static function middleware(): array
{
    return [
        new Middleware(middleware: 'admin', only: [ 'index','show', 'makeAdmin','makeUser','store', 'delete', ]),
       // new Middleware(middleware: 'auth:sanctum', except: ['index', 'show']),
    ];
}


    //Show single user
    public function showVendor(Request $request ) {   
       $user = User::with('profile','groups')->find($request->id);
    // $user = User::findOrFail($request->id);    

    if($user->id != auth()->id()) {
        abort(403, 'Unauthorized Action',);
    }
    return response()->json(['user'=>$user,'status'=>true],200);
    }


    public function updateUser(Request $request)
    {
        $request->validate([
            'first_name' => ['string', 'min:3'],
            'last_name' => ['string', 'min:3'],
            // 'user_role'=>'',
            // 'email' => ['required', 'email', Rule::unique('users', 'email')],
        ]);

        $user = User::find($request->id);
        if($user->id != auth()->id()) {
            abort(403, 'Unauthorized Action',);
        }
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->first_name =$request->first_name;
        $user->last_name =$request->last_name;
        // $user->user_role =$request->user_role;
        $user->save();

        return response()->json(['data'=>$user,'message'=>'user updated '],200);
    }


// JOIN GROUP FUMCTION
    public function joinGroup(Request $request)
    {
        $userId= auth()->id();
        $user = User::with('groups')->findOrFail($userId);
        if(!$user){
            return response()->json(['message' => 'User not found'], 404);
        }
        $request->validate([
            
            'group_id' => 'required',
            'group_id.*' => 'exists:groups,id',
           
        ]);

        $group = Group::find($request->id);
            // Check if the group exists
        if(!$group ){
            return response()->json(['message'=>'group dont exist'],404);
        }

// JIN GROUP WITHOUT DETACHING, USER CAN JOIN GROUP AND REMAIN IN OLD GROUP
        // if ($request->has('group_id')) {
        //     $user->groups()->syncWithoutDetaching([$groupId]);
        //     // $user->groups()->attach($request->group_id);
        // }

         // Check if the user is already in the group
        $alreadyJoined = $user->groups()->where('group_id', $request->id)->exists();

    if ($alreadyJoined) {
        return response()->json(['message' => 'User is already a member of this group'], 400);
    }

    // Attach the user to the group
    $user->groups()->attach($request->id);

        

    

        return response()->json(['message'=>'user Added to group '],200);
    }


    // LEAVE GROUPFUNCTION
    public function leaveGroup(Request $request)
    {
        $userId= auth()->id();
        $user = User::with('groups')->findOrFail($userId);
        if(!$user){
            return response()->json(['message' => 'User not found'], 404);
        }
        $request->validate([
            'group_id' => 'required',
            'group_id.*' => 'exists:groups,id',
           
        ]);

         // Fetch the groups to detach
         $groupIds = $request->input('group_id');

         // Handle single ID by converting it into an array
        if (!is_array($groupIds)) {
            $groupIds = [$groupIds];
        }

        // Detach the user from the groups
        $detached = $user->groups()->detach($groupIds);

        if ($detached) {
            return response()->json(['message' => 'User removed from group(s) successfully'], 200);
        }
        return response()->json(['message' => 'User not removed from group(s) successfully'], 400);
    }

}
