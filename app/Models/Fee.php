<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    
    protected $fillable =[
        'payment_id','user_id','amount','payment_status',
    ];

     // Relationship to user
   public function user(){
    return $this->belongsTo(User::class);
    }

}
