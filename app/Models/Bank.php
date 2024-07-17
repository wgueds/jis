<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable  = [
        'name',
        'description',
        'enable',
    ];

    protected $hidden = [
        'updated_at'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_has_banks');
    }

    public function releases()
    {
        return $this->hasMany(Release::class);
    }
}
