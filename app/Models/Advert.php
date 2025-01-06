<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advert extends Model
{
    protected $fillable=['details','image','title','link'];


    //  // request has many to many relation with group
    //  public function groups()
    //  {
    //      return $this->belongsToMany(Group::class, 'business_request_group');
    //  }
}
