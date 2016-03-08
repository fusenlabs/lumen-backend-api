<?php 

namespace App\Auth;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {
    use Authenticatable, CanResetPassword;
    /**
    * The database table used by the model.
    *
    * @var string
    */
    protected $table = 'users';
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['name', 'email', 'password', 'facebook_id', 'avatar', 'facebook_freinds_ids'];
    /**
    * The attributes excluded from the model's JSON form.
    *
    * @var array
    */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Resolve a connection instance.
     *
     * @param  string|null  $connection
     * @return \Illuminate\Database\Connection
     */
    public static function resolveConnection($connection = null)
    {
        return app()->make('db')->connection();
    }
    
}