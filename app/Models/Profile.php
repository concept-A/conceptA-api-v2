<?php

namespace App\Models;

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    //
    protected $fillable = ['business_name','shop_address','contact','image','subscription'];


     // Relationship To User
     public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

  
    public function categories()
{
    return $this->belongsToMany(Category::class, 'category_profile');
}

    public function groups()
{
    return $this->belongsToMany(Group::class, 'group_profile');
}



    //   // Relationship To group
    //   public function group() {
    //     return $this->hasMany(Group::class, 'group_id');
    // }
    
     

}
