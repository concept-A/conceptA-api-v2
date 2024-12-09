<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable=['name'];

        //   // Relationship To profile
        //   public function profile() {
        //     return $this->hasMany(Profile::class, 'category_id');
        // }

        //  // Relationship To product
        //  public function product() {
        //     return $this->hasMany(Product::class, 'category_id');
        // }

        public function products()
{
    return $this->belongsToMany(Product::class, 'category_product');
}

        public function profiles()
{
    return $this->belongsToMany(Product::class, 'category_profile');
}


}
