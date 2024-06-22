<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Release extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'release_method_id',
        'bank_id',
        'type',
        'title',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function releaseMethod()
    {
        return $this->belongsTo(ReleaseMethod::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
