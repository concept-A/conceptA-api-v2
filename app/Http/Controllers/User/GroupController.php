<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// for middleware in controller
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class GroupController extends Controller
{

    public static function middleware(): array
    {
        return [
           new Middleware(middleware: 'admin', only: [ 'store','delete' ,'update']),
        ];
    }

    // Show all Group
    public function index() {
        $allGroup = Group::all();
        return response()->json( ['Groups' => $allGroup, 'status'=>true], 200);
    }

     //Show single user Group
     public function show(Request $request ) {
        $group = Group::findOrFail($request->id);
        if(!$group){
            return response()->json( ['message' => "You don't have a group, please create one",  'status'=>true],404);
        }
          return response()->json(['Group' => $group,'status'=>true], 200);
      }



      //Show single user Group
      public function showUserGroup(Request $request ) {
        $user = User::with('groups')->findOrFail($request->id);
          if($user->groups->isEmpty()){
            // if(!$user->Group){
              return response()->json(
                  ['message' => "You don't have a Group, please create one",
                  'status'=>true],404);
          }
          $Groups = $user->groups;
          return response()->json(['Groups' => $Groups,'status'=>true], 200);
      }


      //Show Group all for specific group request
      public function groupRequest(Request $request ) {
        // $groupRequests = DB::table('business_requests')->where('group_id', $request->id)->get();
        $groupRequests = Group::with('businessrequests')->find($request->id);

          if($groupRequests->businessrequests->isEmpty()){
          
              return response()->json(
                  ['message' => "You don't have any Group request, please create one",
                  'status'=>true],404);
          }
          return response()->json(['Requests' => $groupRequests->businessrequests,'status'=>true], 200);
      }




    /*****************
     *   // create Group Data
     *********************************/

    // store Business Group Data
    public function store(Request $request) {

      $request->validate([
        'name' => 'required|string|max:255',
        'details' => 'required|string',
        // 'image' => 'nullable',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1048',

       
        ]);
        
         $businessGroup = new Group();
         $businessGroup->details =  $request->details;
         $businessGroup->name =  $request->name;
        
        // $group = Group::create($request->all());
  
        //   //check if image
        if($request->hasFile('image')){
          //upload it
          $businessGroup->image = $request->file('image')->store('groups', 'public');
           }
        
         $businessGroup->save();
          
       return response()->json([
          'message'=> 'Group Added successfully!',
          'Group'=>$businessGroup,'status'=>true], 200);
 
  }


      public function update(Request $request) {
        $request->validate([
            'details' => 'string',
            'name'=>'string',
            // 'name' => 'unique:groups|string',
            'image' => 'nullable',

        ]);

        $group = Group::findOrFail($request->id);

        // Update details and name
        $group->details = $request->input('details');
        $group->name = $request->input('name');

        // Handle image upload
        if ($request->hasFile('image')) {

          if ($group->image) {
            Storage::disk('public')->delete($group->image);
        }
          // Store new image
          $image = $request->file('image')->store('groups', 'public');
            $group->image = $image;
          }

        $group->save();

        return response()->json([
            'message' => 'Group updated successfully!',
            'group' => $group,
            'status' => true
        ], 200);
      }



     //  Delete user and business Group
     public function delete(Request $request) {
        $group = Group::find($request->id);
        
        if($group->image && Storage::disk('public')->exists($group->image)) {
            Storage::disk('public')->delete($group->image);
            
    }
        $group->delete();
        return response()->json([ 'message'=>'group deleted successfully!'],200);

    }


    }


   

