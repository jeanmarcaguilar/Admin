<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'tax_id',
        'notes'
    ];

    /**
     * Get the documents associated with the client.
     */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
