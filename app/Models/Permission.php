<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'type',
        'description',
        'user_id',
        'document_id',
        'access_level',
        'expires_at',
        'created_by',
    ];
    
    protected $casts = [
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Get the user that owns the permission.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the document that this permission applies to.
     */
    public function document()
    {
        return $this->belongsTo(Document::class);
    }
    
    /**
     * Get the user who created this permission.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    /**
     * Check if the permission is expired.
     */
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
    
    /**
     * Check if the permission is active.
     */
    public function isActive()
    {
        return !$this->isExpired();
    }
    
    /**
     * Scope a query to only include active permissions.
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }
    
    /**
     * Scope a query to only include expired permissions.
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }
}
