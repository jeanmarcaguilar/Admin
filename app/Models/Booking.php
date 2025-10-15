<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'type',
        'name',
        'date',
        'start_time',
        'end_time',
        'return_date',
        'quantity',
        'status',
        'purpose',
    ];
}
