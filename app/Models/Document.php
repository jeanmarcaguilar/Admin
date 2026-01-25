<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Client;

class Document extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'type',
        'category',
        'size',
        'status',
        'is_receipt',
        'client_id',
        'visibility',
        'data_type',
        'file_path',
        'file_type',
        'code',
        'description',
        'is_shared',
        'amount',
        'receipt_date',
        'size_label',
        'uploaded_on'
    ];

    protected $casts = [
        'is_receipt' => 'boolean',
        'is_shared' => 'boolean',
        'date' => 'date',
        'receipt_date' => 'date',
        'amount' => 'decimal:2',
        'uploaded_on' => 'date'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
