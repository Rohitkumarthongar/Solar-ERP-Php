<?php

namespace App\Traits;

use App\Models\AdminUser;
use Illuminate\Support\Facades\Auth;

trait Approvable
{
    /**
     * Check if the record requires approval
     */
    public function requiresApproval(): bool
    {
        return $this->approval_status === 'pending';
    }

    /**
     * Check if the record is approved
     */
    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    /**
     * Check if the record is rejected
     */
    public function isRejected(): bool
    {
        return $this->approval_status === 'rejected';
    }

    /**
     * Approve the record
     */
    public function approve(?string $remarks = null): bool
    {
        $userId = session('admin_user_id') ?? Auth::id();
        
        $this->update([
            'approval_status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => now(),
            'approval_remarks' => $remarks,
        ]);

        // Log the approval
        if (method_exists($this, 'logAudit')) {
            $this->logAudit('approved', null, null, 'Approved by manager');
        }

        return true;
    }

    /**
     * Reject the record
     */
    public function reject(string $remarks): bool
    {
        $userId = session('admin_user_id') ?? Auth::id();
        
        $this->update([
            'approval_status' => 'rejected',
            'approved_by' => $userId,
            'approved_at' => now(),
            'approval_remarks' => $remarks,
        ]);

        // Log the rejection
        if (method_exists($this, 'logAudit')) {
            $this->logAudit('rejected', null, null, 'Rejected by manager: ' . $remarks);
        }

        return true;
    }

    /**
     * Reset approval status
     */
    public function resetApproval(): bool
    {
        $this->update([
            'approval_status' => 'pending',
            'approved_by' => null,
            'approved_at' => null,
            'approval_remarks' => null,
        ]);

        return true;
    }

    /**
     * Get the approver
     */
    public function approver()
    {
        return $this->belongsTo(AdminUser::class, 'approved_by');
    }

    /**
     * Get approval status badge class
     */
    public function getApprovalBadgeClassAttribute(): string
    {
        return match($this->approval_status) {
            'approved' => 'bg-green-100 text-green-700',
            'rejected' => 'bg-red-100 text-red-700',
            'pending' => 'bg-yellow-100 text-yellow-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    /**
     * Get approval status icon
     */
    public function getApprovalIconAttribute(): string
    {
        return match($this->approval_status) {
            'approved' => 'fa-check-circle',
            'rejected' => 'fa-times-circle',
            'pending' => 'fa-clock',
            default => 'fa-question-circle',
        };
    }

    /**
     * Scope to get records pending approval
     */
    public function scopePendingApproval($query)
    {
        return $query->where('approval_status', 'pending');
    }

    /**
     * Scope to get approved records
     */
    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    /**
     * Scope to get rejected records
     */
    public function scopeRejected($query)
    {
        return $query->where('approval_status', 'rejected');
    }
}

// Made with Bob
