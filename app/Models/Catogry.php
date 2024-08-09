<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Catogry extends Model
{
    use HasFactory;

    protected $fillable=["name","slug","image","is_active"];


    public function products(){
        return $this->hasMany(Product::class);
    }
}
