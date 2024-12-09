<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    protected $fillable=['image','details','name'];

   
    
    //  // Relationship To group
    //  public function request() {
    //     return $this->hasMany(Request::class, 'group_id');
    // }

    // group has many to many relation with request
    public function businessrequests()
    {
        return $this->belongsToMany(BusinessRequest::class,'business_request_group');
    }


    public function users()
    {
        return $this->belongsToMany(User::class, 'group_user');
    }

//     public function profiles()
// {
//     return $this->belongsToMany(Profile::class, 'group_profile');
// }



}
