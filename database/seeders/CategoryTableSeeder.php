<?php

namespace Database\Seeders;


use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CategoryTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
       
        // $category = Category::where('email', 'admin@email.com')->first();
        // if(!$category){
        //     Category::create([
        //     'first_name'=> 'Anthony',
        //     ]);
        // };

        $category= new Category([ 'name'=> 'Phone Repair' ]);  $category->save() ;
        $category= new Category([ 'name'=> 'Phone Seller' ]);  $category->save() ;
        $category= new Category([ 'name'=> 'Laptop Seller' ]);  $category->save() ;
        $category= new Category([ 'name'=> 'Laptop Repair' ]);  $category->save() ;
        $category= new Category([ 'name'=> 'Others' ]);  $category->save() ;
        


    }
}
