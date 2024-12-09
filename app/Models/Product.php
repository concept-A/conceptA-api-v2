<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable=['image', 'price','details','name'];

// dont cluster group so to ignor
//  // Relationship To group
//  public function group() {
//     return $this->hasMany(Group::class, 'product_id');
// }


     // Relationship To User
     public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    //   // Relationship To group
    //   public function category() {
    //     return $this->hasMany(Category::class, 'product_id');
    // }

    public function categories()
{
    return $this->belongsToMany(Category::class, 'category_product');
}

}
