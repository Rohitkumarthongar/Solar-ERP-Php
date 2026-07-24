<?php

namespace App\Traits;

trait WorkflowLockable
{
    /**
     * Check if the record is locked for editing
     */
    public function isLocked(): bool
    {
        // Check if status field exists
        if (!isset($this->status)) {
            return false;
        }

        // Define locked statuses per model
        $lockedStatuses = $this->getLockedStatuses();
        
        return in_array($this->status, $lockedStatuses);
    }

    /**
     * Get the statuses that lock the record
     */
    protected function getLockedStatuses(): array
    {
        // Default locked statuses
        return ['completed', 'cancelled', 'closed', 'approved'];
    }

    /**
     * Get the next available actions based on current status
     */
    public function getNextActions(): array
    {
        if (!isset($this->status)) {
            return [];
        }

        $actionMap = $this->getStatusActionMap();
        
        return $actionMap[$this->status] ?? [];
    }

    /**
     * Define status-to-actions mapping
     * Override this in models to customize
     */
    protected function getStatusActionMap(): array
    {
        return [
            'scheduled' => [
                ['label' => 'Start Work', 'status' => 'in_progress', 'class' => 'btn-primary'],
                ['label' => 'Cancel', 'status' => 'cancelled', 'class' => 'btn-danger'],
            ],
            'in_progress' => [
                ['label' => 'Mark Complete', 'status' => 'completed', 'class' => 'btn-success'],
                ['label' => 'Cancel', 'status' => 'cancelled', 'class' => 'btn-danger'],
            ],
            'pending' => [
                ['label' => 'Approve', 'status' => 'approved', 'class' => 'btn-success'],
                ['label' => 'Reject', 'status' => 'rejected', 'class' => 'btn-danger'],
            ],
        ];
    }

    /**
     * Get a human-readable lock reason
     */
    public function getLockReason(): string
    {
        if (!$this->isLocked()) {
            return '';
        }

        $reasons = [
            'completed' => 'This record has been completed and cannot be edited.',
            'cancelled' => 'This record has been cancelled and cannot be edited.',
            'closed' => 'This record has been closed and cannot be edited.',
            'approved' => 'This record has been approved and cannot be edited.',
        ];

        return $reasons[$this->status] ?? 'This record is locked and cannot be edited.';
    }

    /**
     * Check if a specific action is allowed
     */
    public function canPerformAction(string $action): bool
    {
        $nextActions = $this->getNextActions();
        
        foreach ($nextActions as $actionData) {
            if (isset($actionData['action']) && $actionData['action'] === $action) {
                return true;
            }
            if (isset($actionData['status']) && $actionData['status'] === $action) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass(): string
    {
        $badgeMap = [
            'scheduled' => 'badge-info',
            'pending' => 'badge-warning',
            'in_progress' => 'badge-primary',
            'completed' => 'badge-success',
            'cancelled' => 'badge-danger',
            'rejected' => 'badge-danger',
            'approved' => 'badge-success',
            'closed' => 'badge-secondary',
        ];

        return $badgeMap[$this->status ?? 'pending'] ?? 'badge-secondary';
    }
}

// Made with Bob
