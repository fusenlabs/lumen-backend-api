<?php 

namespace App;

use Illuminate\Database\Eloquent\Model;

class Score extends Model {
    /**
    * The database table used by the model.
    *
    * @var string
    */
    protected $table = 'scores';

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['user_id', 'genre', 'points'];
    
}