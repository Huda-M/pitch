<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investors extends Model
{
    use HasFactory;

    protected $fillable = [
        'focus_field',
        'company',
        'min_charge',
        'max_charge',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


