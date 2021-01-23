<?php

namespace Modules\Auth\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Modules\Location\Models\Province;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;
    use HasApiTokens;

    protected $table = 'user_module_users';

    public function getTableName()
    {
        return $this->table;
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roleFinder()
    {
        return $this->hasMany('Role');
    }

    public function roleCheck($role_name)
    {

        if ($this->hasRole([$role_name])) {
            return true;
        }

        return false;
    }

    public function detachAllRoles()
    {
        \DB::table('user_role_user')->where('user_id', $this->id)->delete();

        return $this;
    }
}
