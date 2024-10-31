<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
class User extends Eloquent
{
    protected $table = 'users';
    protected $guarded = [];

    public function group(){
        return $this->belongsTo(Group::class);
    }
    public function factory(){
        return $this->belongsTo(Factory::class,'group_id');
    }
    public function latestLog()
    {
        return $this->hasOne(CustomerLog::class, 'user_id')->latest();
    }
    public function logs(){
        return $this->hasMany(CustomerLog::class,'user_id');
    }

    public function orders(){
        return $this->hasMany(Order::class);
    }

    public function active(){
        return $this->hasMany(UserActiveLog::class);
    }
    public function latestOrder()
    {
        return $this->hasOne(Order::class)->latest();
    }

    public function role(){
        return $this->belongsTo(Role::class);
    }

    public function customers(){
        return $this->hasMany(Customer::class);
    }

    public function transactions(){
        return $this->hasMany(Transaction::class);
    }

    
    public function checkAdminXuong(){
        return in_array($this->role_id,[1,8]);
    }
}
