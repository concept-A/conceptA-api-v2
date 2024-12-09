<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use GuzzleHttp\Exception\ClientException;

class SocialLoginController extends Controller
{

public function redirectToProvider($driver)
{
    $validated = $this->validateProvider($driver);

    if(!is_null($validated)){
        return $validated;
    }

    return Socialite::driver($driver)->stateless()->redirect();
}


public function handleProviderCallback($driver)
{
    $validated = $this->validateProvider($driver);

    if(!is_null($validated)){
        return $validated;
    }
    try {
        $user = Socialite::driver($driver)->stateless()->user();
    } catch (ClientException $exception) {
        return response()->json(['error' => 'invalid credentials provided.'], 422);

    }

    
    $newUser = User::updateOrCreate([
        'provider_name' => $driver,
        'provider_id' => $user->getId(),  
    ], [
        'name' => $user->getName(),
        'email' => $user->getEmail(),  
        'email_verified_at' =>now(),
         
        // you can also get avatar, so create avatar column in database it you want to save profile image
       // 'avatar' => $newUser->getAvatar(),
    ]);
 
        

    $token = $newUser->createToken('myapptoken')->plainTextToken;
       
    $response = [
        'message' => 'Registration successful.',
         'user'=> $newUser, 'token' => $token   ];

    return response()->json($response, 200);
    }
    


protected function validateProvider($driver)
{
    if(!in_array($driver, ['google','facebook'])){
        return response()->json(['error' => 'Please login using google or facebook'], 422);
    }
}

}

