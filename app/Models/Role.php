<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'permissions'];

    protected $casts = [
        'permissions' => 'array', // otomatis decode JSON ke array
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }
}
