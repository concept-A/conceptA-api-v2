<?php

namespace App\Http\Controllers\Advert;

use App\Models\Advert;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class AdvertController extends Controller
{


public static function middleware(): array
{
    return [
        new Middleware(middleware: 'admin', only: [ 'store','update','delete']),
    ];
}

    // Show all profile
    public function index() {
        $allAdvert = Advert::all();
        return response()->json( ['Adverts' => $allAdvert, 'status'=>true], 200);
    }

      //Show single product Category
      public function show(Request $request ) {
        $advert = Advert::findOrFail($request->id);
          return response()->json(['Advert' => $advert,'status'=>true], 200);
      }


    /*****************
     *   // Store product category Data
     *********************************/
  
    public function store(Request $request) {
      $validatedField =  $request->validate([
        //Remember to make unique
        'link' => 'string',
        'title' => 'string',
        'details' => 'required|string',
        'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:1048',
    ]);
        $advert = new Advert($validatedField);  

        if($request->hasFile('image')) {
            $advert->image = $request->file('image')->store('adverts', 'public');
        }
    
        $advert->save();
        return response()->json([
            'message'=> 'created successfully!', 'Advert'=> $advert,'status'=>true], 200);
    }
    ///////////////////////UPDATE/////////////////

    public function update(Request $request) {
        $request->validate([
        'details' => 'string',
        'link' => 'string',
        'title' => 'string',
        // 'image' => 'nullable',
        'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:1048',

          ]);


          $advert = Advert::findOrFail($request->id);
          $advert->details = $request->details;
          $advert->title = $request->title;
          $advert->link = $request->link;

              //check if image
              if($request->hasFile('image')){
                //upload it
                $image = $request->file('image')->store('adverts', 'public');
                //delete former image
                Storage::disk('public')->delete($advert->image);
                $advert->image = $image;
                 }

          $advert->save();

          return response()->json([
              'message'=> 'advert updated successfully!',
              'Advert'=> $advert,'status'=>true], 200);
      }


    //Delete profile
    public function delete(Request $request) {
        $advert = Advert::findOrFail($request->id);

        if($advert->image && Storage::disk('public')->exists($advert->image)) {
            Storage::disk('public')->delete($advert->image);     
        }
    
            $advert->delete();
        return response()->json([ 'message'=>'Advert deleted successfully!'],200);
    }

}
