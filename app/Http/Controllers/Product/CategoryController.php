<?php

namespace App\Http\Controllers\Product;


use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class CategoryController extends Controller
{


public static function middleware(): array
{
    return [
        new Middleware(middleware: 'admin', only: [ 'store','update']),
    ];
}


    // Show all profile
    public function index() {
        $allProfile = Category::all();
        return response()->json( ['Categories' => $allProfile, 'status'=>true], 200);
    }

      //Show single product Category
      public function show(Request $request ) {
        $category = Category::findOrFail($request->id);
          return response()->json(['Category' => $category,'status'=>true], 200);
      }



    /*****************
     *   // Store product category Data
     *********************************/
  
    public function store(Request $request) {
      $validatedField =  $request->validate([
        //Remember to make unique
        // 'name' => 'required'
        'name' => 'required|unique:categories|string',
        
    ]);
        $category = new Category($validatedField);  
        $category->save();
        return response()->json([
            'message'=> 'created successfully!', 'category'=> $category,'status'=>true], 200);
    }

    public function update(Request $request) {
        $request->validate([
            //   'name' => 'required'
        'name' => 'unique:categories|string|required',

          ]);

          $category = Category::findOrFail($request->id);
          $category->name = $request->name;
          $category->save();

          return response()->json([
              'message'=> 'category updated successfully!',
              'category'=> $category,'status'=>true], 200);
      }


    //Delete profile
    public function delete(Request $request) {

         
        $cat =Category::findOrFail($request->id);
            $cat->delete();
        return response()->json([ 'message'=>'category deleted successfully!'],200);
    }

}
