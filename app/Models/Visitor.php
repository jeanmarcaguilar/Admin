<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'company',
        'visitor_type',
        'host',
        'host_department',
        'check_in_date',
        'check_in_time',
        'check_out_date',
        'check_out_time',
        'purpose',
        'status',
    ];
}
