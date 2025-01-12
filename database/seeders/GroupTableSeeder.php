<?php

namespace Database\Seeders;


use App\Models\Group;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GroupTableSeeder extends Seeder
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

        $group= new Group([ 'name'=> 'Phone Repair','details'=> 'We Repair Phone ','image'=> null, ]);  $group->save() ;
        $group= new Group([ 'name'=> 'Phone Seller','details'=> 'We sell Phone ','image'=> null, ]);  $group->save() ;
        $group= new Group([ 'name'=> 'Laptop Repair','details'=> 'We Repair Laptop ','image'=> null, ]);  $group->save() ;
        $group= new Group([ 'name'=> 'Laptop Seller','details'=> 'We sell Laptop','image'=> null, ]);  $group->save() ;
     
        


    }
}
