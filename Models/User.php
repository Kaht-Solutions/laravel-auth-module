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

    protected $table = 'usermodule_users';

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
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function role_finder()
    {
        return $this->hasMany('Role');
    }

    public function role_ch($role_name)
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

    // public function roles()
    // {
    //     return $this->belongsToMany('Modules\Auth\Models\Role');
    // }

    public function get_top_role($return_name = false)
    {
        if ($return_name) {
            if ($this->hasRole('admin')) {
                return 'مدیر کل';
            } elseif ($this->hasRole('reseller')) {
                return 'ریسلر';
            } elseif ($this->hasRole('accountant')) {
                $province = Province::find($this->province_id);
                return 'حسابدار' . ' - ' . $province->name;
            } elseif ($this->hasRole('head_accountant')) {
                return 'حسابدار کل';
            } elseif ($this->hasRole('province_manager')) {
                $province = Province::find($this->province_id);
                return 'مدیر استانی' . ' - ' . $province->name;
            } elseif ($this->hasRole('backup')) {
                $province = Province::find($this->province_id);
                return 'پشتیبان' . ' - ' . $province->name;
            } elseif ($this->hasRole('head_backup')) {
                return 'پشتیبان کل';
            }
            return '';
        } else {
            if ($this->hasRole('admin')) {
                return 'admin';
            } elseif ($this->hasRole('reseller')) {
                return 'seller';
            } elseif ($this->hasRole('accountant')) {
                return 'accountant';
            } elseif ($this->hasRole('head_accountant')) {
                return 'head_accountant';
            } elseif ($this->hasRole('province_manager')) {
                return 'province_manager';
            } elseif ($this->hasRole('backup')) {
                return 'backup';
            } elseif ($this->hasRole('head_backup')) {
                return 'head_backup';
            }
            return '';
        }
    }
}
