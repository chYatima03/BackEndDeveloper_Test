<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parents extends Model
{

    use HasFactory;
    protected $table = 'parents';
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    // protected $fillable = [
    //     'name', 'route','icon', 'is_children',
    // ];
    protected $hidden = [
        'updated_at',
        'created_at',
    ];

    public function Children()
    {
        return $this->hasMany(Childrens::class, 'parent_id', 'id');
    }

}
