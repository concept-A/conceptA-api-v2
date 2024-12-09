<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordNotification;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements MustVerifyEmail
{
    
    use HasApiTokens, HasFactory, Notifiable;

   //private $clientBaseUrl= 'https://spa.test/reset-password?token=';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'user_role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

     // // Relationship To profile
    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id');
    }
   

    //  // Relationship To group
    //  public function group() {
    //     return $this->hasMany(Group::class, 'user_id');
    // }

      // Relationship To group
      public function product() {
        return $this->hasMany(Product::class, 'user_id');
    }

    // Relationship To group
    public function businessrequest() {
        return $this->hasMany(BusinessRequest::class, 'user_id');
    }

    //    //  // user has many to many relation with group
    //    public function groups(): BelongsToMany
    //    {
    //        return $this->belongsToMany(Group::class);
    //    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_user');
    }

   
  
}
