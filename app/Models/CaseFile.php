<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CaseFile extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'number',
        'name',
        'type_label',
        'type_badge',
        'client',
        'client_org',
        'client_initials',
        'status',
        'hearing_date',
        'hearing_time',
        'contract_type',
        'contract_number',
        'contract_date',
        'contract_expiration',
        'contract_notes',
        'created_by',
    ];
    
    protected $casts = [
        'contract_date' => 'date',
        'contract_expiration' => 'date',
        'hearing_date' => 'date',
    ];
    
    /**
     * Get the client associated with the case file.
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
    
    /**
     * Get the user assigned to the case.
     */
    public function assignedUser()
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_to');
    }
    
    // Contract types
    const CONTRACT_TYPE_EMPLOYEE = 'employee';
    const CONTRACT_TYPE_EMPLOYMENT = 'employment';
    const CONTRACT_TYPE_SERVICE = 'service';
    const CONTRACT_TYPE_OTHER = 'other';
    
    /**
     * Get the contract type label
     */
    public function getContractTypeLabelAttribute()
    {
        return [
            self::CONTRACT_TYPE_EMPLOYEE => 'Employee Contract',
            self::CONTRACT_TYPE_EMPLOYMENT => 'Employment Agreement',
            self::CONTRACT_TYPE_SERVICE => 'Service Contract',
            self::CONTRACT_TYPE_OTHER => 'Other Agreement',
        ][$this->contract_type] ?? 'No Contract';
    }
    
    /**
     * Check if contract is expired
     */
    public function getIsExpiredAttribute()
    {
        if (empty($this->contract_expiration)) {
            return false;
        }
        return $this->contract_expiration->isPast();
    }
    
    /**
     * Get contract status
     */
    public function getContractStatusAttribute()
    {
        if (empty($this->contract_type)) {
            return 'No Contract';
        }
        
        if ($this->is_expired) {
            return 'Expired';
        }
        
        if ($this->contract_expiration && $this->contract_expiration->isFuture()) {
            return 'Active';
        }
        
        return 'Inactive';
    }
}
