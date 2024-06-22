<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = ['release_id', 'status_id', 'payment_date'];

    public  function release()
    {
        return $this->belongsTo(Release::class);
    }

    public  function status()
    {
        return $this->belongsTo(Status::class);
    }
}
