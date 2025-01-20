<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\BusinessRequest;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

// for middleware in controller
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

use function PHPUnit\Framework\isEmpty;

class BusinessRequestController extends Controller
{

    public static function middleware(): array
    {
        return [
            new Middleware(middleware: 'admin', only: [ 'index']),
        ];
    }

    // Show all BusinessRequest
    public function index() {
        $allBusinessRequest = BusinessRequest::with('groups')->get();
        return response()->json( ['Requests' => $allBusinessRequest, 'status'=>true], 200);
    }

      //Show single user BusinessRequest
      public function show(Request $request ) {
        $BusinessRequest = BusinessRequest::findOrFail($request->id);
         
        if($BusinessRequest->isEmpty()){
              return response()->json(
                  ['message' => "You don't have a BusinessRequest, please create one",
                  'status'=>true],404);
          }
          return response()->json(['Request' => $BusinessRequest,'status'=>true], 200);
      }


        //Show single user BusinessRequest
        public function showUserRequest(Request $request ) {
            $user = User::with('BusinessRequest.groups')->findOrFail($request->id);
        
              if($user->BusinessRequest->isEmpty()){
                // if(!$user->BusinessRequest){
                  return response()->json(
                      ['message' => "You don't have a BusinessRequest, please create one",
                      'status'=>true],404);
              }
              $BusinessRequest = $user->BusinessRequest;
              return response()->json(['Requests' => $BusinessRequest,'status'=>true], 200);
          }
    


    /*****************
     *   // Store Business BusinessRequest Data
     *********************************/
  
    public function store(Request $request) {
        $group = Group::all();
        if (($group->isEmpty()) ) {
            // if (!$group) {
            return response()->json(
                ['message' => "pls add group, categories empty",
                'status'=>false ],401);
        }
       $request->validate([
        'title' => 'required|string|max:255',
        'details' => 'required|string',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:1048',
        'group_id' => 'required|array',
        'group_id.*' => 'exists:groups,id', // Ensure each group exists
        ]);
        
           $businessRequest = new businessRequest();
           $businessRequest->details =  $request->details;
           $businessRequest->title =  $request->title;

    if($request->hasFile('image')) {
        $businessRequest->image = $request->file('image')->store('requests', 'public');
    }

    $businessRequest->user_id = auth()->id();
    // $businessRequest->user_id = 1;
    $businessRequest->save(); 

    if ($request->has('group_id')) {
        $businessRequest->groups()->sync($request->group_id);
    }

        return response()->json([
            'message'=> 'businessRequest created successfully!',
            'Request'=> $businessRequest,'status'=>true], 200);
    }


    // Update Business BusinessRequest Data
    public function update(Request $request) {
        $businessRequest = BusinessRequest::findOrFail($request->id);

        if(empty($businessRequest)){
            return response()->json(['message' => "You don't have a Request, please create one",'status'=>true]);
        }
     // Make sure logged in user is owner
        if($businessRequest->user_id != auth()->id() ) {
            abort(403, 'Unauthorized Action',);
        }
        
        $request->validate([
            'title' => 'nullable|string|max:255', // Only validate if present
            'details' => 'nullable|string', // Only validate if present
             'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1048',
            'group_id' => 'nullable|array',
            'group_id.*' => 'exists:groups,id',
            // 'image'=>'nullable',
        ]);
    
        $businessRequest->details =  $request->details;
        $businessRequest->title =  $request->title;

        if ($request->hasFile('image')) {
            // if ($businessRequest->image) {
            //     Storage::disk('public')->delete($businessRequest->image);
            // }
            Storage::disk('public')->delete($businessRequest->image);
            $businessRequest->image = $request->file('image')->store('requests', 'public');
        }
       
        $businessRequest->user_id = auth()->id();
        // $businessRequest->user_id = 1;
        $businessRequest->save(); 
    
        if ($request->has('group_id')) {
            $businessRequest->groups()->sync($request->group_id);
        }
    
            return response()->json([
                'message'=> 'businessRequest updated successfully!',
                'Request'=> $businessRequest,'status'=>true], 200);
        }




//  Delete user and business BusinessRequest
public function delete(Request $request) {
    $BusinessRequest = BusinessRequest::findOrFail($request->id)->first();
    
    if($BusinessRequest->image && Storage::disk('public')->exists($BusinessRequest->image)) {
        Storage::disk('public')->delete($BusinessRequest->image);
        
}
//    $user->delete();
     $BusinessRequest->delete();
     return response()->json([ 'message'=>'Request deleted successfully!'],200);

}

}
