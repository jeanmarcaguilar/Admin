<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplianceTracking extends Model
{
    use HasFactory;

    protected $table = 'compliance_tracking';

    protected $fillable = [
        'code',
        'title',
        'type',
        'status',
        'due_date',
        'description',
        'responsible_person',
        'priority'
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    // Accessor for formatted type
    public function getTypeFormattedAttribute()
    {
        return ucfirst($this->type);
    }

    // Accessor for formatted status
    public function getStatusFormattedAttribute()
    {
        return ucfirst($this->status);
    }

    // Accessor for status badge classes
    public function getStatusBadgeClassesAttribute()
    {
        switch ($this->status) {
            case 'active':
                return 'bg-green-100 text-green-800';
            case 'pending':
                return 'bg-yellow-100 text-yellow-800';
            case 'overdue':
                return 'bg-red-100 text-red-800';
            case 'completed':
                return 'bg-blue-100 text-blue-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    // Accessor for priority badge classes
    public function getPriorityBadgeClassesAttribute()
    {
        switch ($this->priority) {
            case 'critical':
                return 'bg-red-100 text-red-800';
            case 'high':
                return 'bg-orange-100 text-orange-800';
            case 'medium':
                return 'bg-yellow-100 text-yellow-800';
            case 'low':
                return 'bg-green-100 text-green-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    // Scope for active compliance items
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope for pending compliance items
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope for overdue compliance items
    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    // Scope for due this month
    public function scopeDueThisMonth($query)
    {
        return $query->whereBetween('due_date', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ]);
    }

    // Scope for at risk (due within 7 days)
    public function scopeAtRisk($query)
    {
        return $query->where('due_date', '<', now()->addDays(7))
                    ->whereIn('status', ['active', 'pending']);
    }

    // Derived flag: at risk if due within next 7 days (inclusive) and not past due
    public function getIsAtRiskAttribute(): bool
    {
        if (!$this->due_date) return false;
        $today = now()->startOfDay();
        $in7Days = now()->addDays(7)->endOfDay();
        $isActionable = in_array($this->status, ['active','pending']);
        return $isActionable && $this->due_date->gte($today) && $this->due_date->lte($in7Days);
    }

    // Badge classes for At Risk indicator
    public function getAtRiskBadgeClassesAttribute(): string
    {
        return 'bg-orange-100 text-orange-800';
    }
}
