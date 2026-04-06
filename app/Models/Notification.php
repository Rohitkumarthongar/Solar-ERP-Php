<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'title',
        'message',
        'type',
        'priority',
        'related_id',
        'related_type',
        'action_url',
        'recipient_user_id',
        'is_read',
        'read_at'
    ];
    
    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];

    public function recipientUser()
    {
        return $this->belongsTo(AdminUser::class, 'recipient_user_id');
    }

    /**
     * Generate action URL based on related type and ID
     */
    public function getActionUrlAttribute($value)
    {
        // Return stored URL if exists
        if ($value) {
            return $value;
        }

        // Generate URL based on related type
        if (!$this->related_type || !$this->related_id) {
            return null;
        }

        $routes = [
            'Installation' => 'admin.installations.show',
            'SiteVisit' => 'admin.site-visits.show',
            'ServiceRequest' => 'admin.services.show',
            'Lead' => 'admin.leads.show',
            'SalesOrder' => 'admin.sales-orders.show',
            'Customer' => 'admin.customers.show',
            'TaskPayment' => 'admin.employees.payments',
        ];

        $modelClass = class_basename($this->related_type);
        
        if (isset($routes[$modelClass])) {
            return route($routes[$modelClass], $this->related_id);
        }

        return null;
    }

    /**
     * Get priority badge color
     */
    public function getPriorityBadgeClass(): string
    {
        return match($this->priority) {
            'urgent' => 'badge-danger',
            'high' => 'badge-warning',
            'normal' => 'badge-info',
            'low' => 'badge-secondary',
            default => 'badge-info',
        };
    }

    /**
     * Get priority icon
     */
    public function getPriorityIcon(): string
    {
        return match($this->priority) {
            'urgent' => '🔴',
            'high' => '🟠',
            'normal' => '🔵',
            'low' => '⚪',
            default => '🔵',
        };
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for priority notifications
     */
    public function scopePriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for high priority (high + urgent)
     */
    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'urgent']);
    }
}
