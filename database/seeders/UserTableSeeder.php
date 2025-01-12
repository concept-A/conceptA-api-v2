<?php

namespace Database\Seeders;


use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
       
        $user = User::where('email', 'anthony@email.com')->first();
        if(!$user){
            User::create([
            'first_name'=> 'Anthony',
            'last_name'=> 'Esekie',
            'email'=> 'anthony@email.com',
            'user_role' =>'admin',
            'email_verified_at' => now(),
            'password' => Hash::make('Anthony@123')
            ]);
        };

        $user= new User([                                    
            'first_name'=> 'Mona',
            'last_name'=> 'Adejoh',
            'email'=> 'mona@email.com',
            'user_role' => 'admin',
           'email_verified_at' => now(),
           'password' => Hash::make('Mona@123')     
              ]);  $user->save() ;
        $user= new User([                                    
        'first_name'=> 'John',
        'last_name'=> 'Doe',
        'email'=> 'user@email.com',
        'user_role' => 'user',
        'email_verified_at' => now(),
        'password' => Hash::make('user@123')     
            ]);  $user->save() ;
    }
}
