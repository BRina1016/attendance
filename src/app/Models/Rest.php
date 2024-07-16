<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rest extends Model
{
    use HasFactory;

    protected $primaryKey = 'rest_id';

    protected $fillable = [
        'user_id',
        'stamp_id',
        'rest_start',
        'rest_end',
        'rest_time'
    ];

    protected $dates = ['rest_start', 'rest_end'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stamp()
    {
        return $this->belongsTo(Stamp::class, 'stamp_id', 'stamp_id');
    }
}
