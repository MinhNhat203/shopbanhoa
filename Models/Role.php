<?php

namespace App\Models;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Role extends Authenticatable
{
    use Notifiable;

    protected $table = 'roles'; // Đảm bảo model trỏ đúng bảng

    protected $fillable = [
        'name', 'description',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    public function users() {
        return $this->hasMany(User::class);
    }
}

