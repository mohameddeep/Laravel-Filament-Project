<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded=[];


    public $casts=["images" =>'array'];


    public function orderItems(){
        return $this->hasMany(OrderItem::class);
    }

    public function catogry(){
        return $this->belongsTo(Catogry::class,"catogry_id");
    }
    public function brand(){
        return $this->belongsTo(Brand::class);
    }
}
