<?php

namespace App\Http\Controllers\Product;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

// for middleware in controller
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class ProductController extends Controller
{

    // public static function middleware(): array
    // {
    //     return [
    //         new Middleware(middleware: 'admin', only: [ 'index']),
    //     ];
    // }

    // Show all Product
    public function index() {
        // $allProduct = Product::all();
        $allProduct=  Product::with('categories')->get();
        return response()->json( ['Products' => $allProduct, 'status'=>true], 200);
    }

    
     ////////////////////////////////////////////////////////////////////////
         //Show single Product
         public function show(Request $request ) {
            $Product = Product::with('categories')->findOrFail($request->id);
              if(empty( $Product)){
                  return response()->json(
                      ['message' => " product does not exist",
                      'status'=>true],404);
              }
              return response()->json(['Product' => $Product,'status'=>true], 200);
          }
    
////////////////////////////////////////////////////////////////////////
         //Show All user Products
         public function showUserProduct(Request $request ) {
          $user = User::with('Product.categories')->findOrFail($request->id);
          // if($user->id != auth()->id()) {
          //     abort(403, 'Unauthorized Action',);
          // }
            if(empty( $user->product)){
              // if(!$user->Product){
                return response()->json(
                    ['message' => "You don't have a Product, please create one",
                    'status'=>true],404);
            }
            $Product = $user->Product;
            return response()->json(['Products' => $Product,'status'=>true], 200);
        }
  

////// //Show all product by Category
      public function showByCategory(Category $category)
        {
            // Get products related to the category
            $products = $category->products;

            // Check if the category has any products
            if ($products->isEmpty()) {
                return response()->json([
                    'message' => "No products found in this category.",
                    'status' => false,
                ], 404);
            }

            // Return the products
            return response()->json([
                'Products' => $products,
                'status' => true,
            ], 200);
        }

    /*****************
     *   // Store Business Product Data
     *********************************/
  
    public function store(Request $request) {
        $category = Category::all();
        if ($category->isEmpty() ) {
            // if (!$category) {
            return response()->json(
                ['message' => "pls add categories, categories empty",
                'status'=>false ],401);
        }
       $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:1048',
            'price' => 'required',
            'details' => 'required',
            'name' => 'required',
            'category_id' => 'required|array', // Ensure category_id is an array
            'category_id.*' => 'exists:categories,id', // Validate that each category ID exists
        ]);
        
           $businessProduct = new Product();
           $businessProduct->image = $request->image;
           $businessProduct ->price= $request->price;
           $businessProduct->details =  $request->details;
          //  $businessProduct->category_id =  $request->category_id;
           $businessProduct->name =  $request->name;

    if($request->hasFile('image')) {
        $businessProduct->image = $request->file('image')->store('products', 'public');
    }

    $businessProduct->user_id = auth()->id();
    
    // $businessProduct->user_id = 1;
    $businessProduct->save(); 
      // Attach categories to the product
      if ($request->has('category_id')) {
        $businessProduct->categories()->sync($request->category_id);
    }

        return response()->json([
            'message'=> 'Product created successfully!',
            'Product'=> $businessProduct,'status'=>true], 200);
    }


        ///////////////////////////
    ////// UPDATE PRODUCT/////////////

        public function update(Request $request)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1048',
            'price' => 'nullable|numeric',
            'details' => 'nullable|string',
            'name' => 'nullable|string',
            'category_id' => 'array', // Ensure category_id is an array
            'category_id.*' => 'exists:categories,id', // Validate that each category ID exists
        ]);

        $businessProduct = Product::findOrFail($request->id);

        // Check if the user is authorized to update the product
        if (auth()->user()->user_role != 'admin' && $businessProduct->user_id != auth()->id()) {
            abort(403, 'Unauthorized Action');
        }

        // Update product fields
        $businessProduct->price = $request->price ?? $businessProduct->price;
        $businessProduct->details = $request->details ?? $businessProduct->details;
        $businessProduct->name = $request->name ?? $businessProduct->name;

        // Check if a new image is uploaded
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($businessProduct->image && Storage::disk('public')->exists($businessProduct->image)) {
                Storage::disk('public')->delete($businessProduct->image);
            }

            // Upload the new image
            $image = $request->file('image')->store('products', 'public');
            $businessProduct->image = $image;
        }

        // Save updated product
        $businessProduct->save();

        // Attach categories to the product
        if ($request->has('category_id')) {
            $businessProduct->categories()->sync($request->category_id);
        }

        return response()->json([
            'message' => 'Product updated successfully!',
            'Product' => $businessProduct,
            'status' => true,
        ], 200);
    }


//  Delete user and business product
public function delete(Request $request) {
    $product = Product::find($request->id);

//     if($product->user_id != auth()->id() ) {
//       abort(403, 'Unauthorized Action',);
//   }   
    if($product->image && Storage::disk('public')->exists($product->image)) {
        Storage::disk('public')->delete($product->image);     
    }

     $product->delete();
     return response()->json([ 'message'=>'Product deleted successfully!'],200);

}

}
