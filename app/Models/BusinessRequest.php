<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BusinessRequest extends Model
{
    protected $fillable=['title','details','image',];

//      // Relationship To group
//  public function group() {
//     return $this->hasMany(Group::class, 'request_id');
// }

     // Relationship To User
     public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

     // request has many to many relation with group
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'business_request_group');
    }
    

    
}
