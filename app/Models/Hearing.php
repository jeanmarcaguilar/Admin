<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hearing extends Model
{
    protected $fillable = [
        'title',
        'type',
        'case_number',
        'hearing_date',
        'hearing_time',
        'court_location',
        'judge',
        'status',
        'priority',
        'description',
        'responsible_lawyer',
        'client_name',
        'case_type',
        'reminder_sent'
    ];

    protected $casts = [
        'hearing_date' => 'date',
        'reminder_sent' => 'boolean'
    ];
}
