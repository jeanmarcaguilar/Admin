<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchivalSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'default_retention',
        'auto_archive',
        'notification_emails',
    ];
}
