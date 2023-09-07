<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beers extends Model
{
   use HasFactory;

   protected $database = 'mysql';
   protected $table = 'beers';


    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'type_beer_id', 'beer_name', 'beer_image', 'beer_detail'
    ];
     protected $hidden = [
        'updated_at',
        'created_at',
    ];

    public function types() {
        return $this->belongsTo(Typebeers::class,'type_beer_id','id');
   }



}
