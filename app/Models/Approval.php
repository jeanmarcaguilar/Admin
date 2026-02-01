<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    protected $fillable = [
        'title',
        'description',
        'status',
        'requester_id',
        'approver_id',
        'type',
        'requested_by',
        'date',
        'lead_time',
        'approved_by',
        'rejected_by',
        'approved_at',
        'rejected_at',
        'request_id'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'date' => 'date',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
