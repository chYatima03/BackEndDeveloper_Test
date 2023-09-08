<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Childrens extends Model
{
    use HasFactory;
    protected $database = 'mysql';
    protected $table = 'childrens';
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'parent_id','name', 'route',
    ];

    public function Parent_child()
    {
        return $this->belongsTo(Parents::class,'parent_id','id');
    }


}
