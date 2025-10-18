<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hearing extends Model
{
<<<<<<< HEAD
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
=======
    //
>>>>>>> 3467a8cdf3aef1c3632815755eba1f09b252a719
}
