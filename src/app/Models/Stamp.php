<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stamp extends Model
{
    use HasFactory;

    protected $primaryKey = 'stamp_id';

    protected $fillable = [
        'user_id',
        'clock_in',
        'clock_out',
        'work_time'
    ];

    protected $dates = ['clock_in', 'clock_out'];

    public function user()
    {
        return $this->belongsTo(User::class);
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function rests()
    {
        return $this->hasMany(Rest::class, 'stamp_id', 'stamp_id');
    }
}
