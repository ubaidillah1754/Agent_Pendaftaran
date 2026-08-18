<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointRedemption extends Model
{
    protected $fillable = ['user_id', 'points', 'type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
