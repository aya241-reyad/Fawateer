<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded =[];  

protected $dates = ['deleted_at'];

     public function sections()
   {
   return $this->belongsTo('App\Models\Section','section');
   }
    
}
