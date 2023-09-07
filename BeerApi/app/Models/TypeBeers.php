<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Typebeers extends Model
{
//    use HasFactory;

   protected $database = 'mysql';
   protected $table = 'type_beers';


    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'type_name',
    ];
    public function beer() {
        return $this->hasMany(Beers::class);
   }

}
