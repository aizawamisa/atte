<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Work extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'start_work',
        'end_work'
        ];
    
        protected $dates = [
            'start_work',
            'end_work',
        ];

    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

    // public function rests()
    // {
    //     return $this->hasMany(Rest::class, 'work_id');
    // }
}
