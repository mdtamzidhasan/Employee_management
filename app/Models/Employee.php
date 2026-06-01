<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'position',
        'department',
        'salary',
        'joining_date',
        'address',
        'profile_photo',
        'status',
    ];

    protected $casts = [
        'joining_date' => 'date',
        'salary' => 'decimal:2',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
