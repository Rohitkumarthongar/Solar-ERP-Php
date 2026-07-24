<?php

namespace App\Traits;

use App\Models\AuditLog;

trait Auditable
{
    protected static function bootAuditable()
    {
        static::created(function ($model) {
            if (auth('admin')->check()) {
                AuditLog::log('created', $model, "{$model->getAuditName()} created");
            }
        });

        static::updated(function ($model) {
            if (auth('admin')->check()) {
                $changes = $model->getChanges();
                $original = $model->getOriginal();
                
                // Filter out timestamps and irrelevant fields
                $relevantChanges = array_diff_key($changes, array_flip(['updated_at']));
                
                if (!empty($relevantChanges)) {
                    $oldValues = array_intersect_key($original, $relevantChanges);
                    
                    // Check for status changes
                    if (isset($relevantChanges['status'])) {
                        AuditLog::log(
                            'status_changed',
                            $model,
                            "{$model->getAuditName()} status changed from '{$oldValues['status']}' to '{$relevantChanges['status']}'",
                            $oldValues,
                            $relevantChanges
                        );
                    }
                    
                    // Check for team/assignment changes
                    if (isset($relevantChanges['assigned_team_id']) || isset($relevantChanges['assigned_employee_id'])) {
                        AuditLog::log(
                            'reassigned',
                            $model,
                            "{$model->getAuditName()} reassigned",
                            $oldValues,
                            $relevantChanges
                        );
                    }
                    
                    // Check for approval changes
                    if (isset($relevantChanges['is_approved'])) {
                        $action = $relevantChanges['is_approved'] ? 'approved' : 'unapproved';
                        AuditLog::log(
                            $action,
                            $model,
                            "{$model->getAuditName()} {$action}",
                            $oldValues,
                            $relevantChanges
                        );
                    }
                    
                    // General update log
                    if (!isset($relevantChanges['status']) && 
                        !isset($relevantChanges['assigned_team_id']) && 
                        !isset($relevantChanges['assigned_employee_id']) &&
                        !isset($relevantChanges['is_approved'])) {
                        AuditLog::log(
                            'updated',
                            $model,
                            "{$model->getAuditName()} updated",
                            $oldValues,
                            $relevantChanges
                        );
                    }
                }
            }
        });

        static::deleted(function ($model) {
            if (auth('admin')->check()) {
                AuditLog::log('deleted', $model, "{$model->getAuditName()} deleted");
            }
        });
    }

    /**
     * Get a human-readable name for audit logs
     */
    public function getAuditName(): string
    {
        $class = class_basename($this);
        
        // Try to get a meaningful identifier
        if (isset($this->installation_number)) {
            return "{$class} #{$this->installation_number}";
        }
        if (isset($this->ticket_number)) {
            return "{$class} #{$this->ticket_number}";
        }
        if (isset($this->visit_number)) {
            return "{$class} #{$this->visit_number}";
        }
        if (isset($this->name)) {
            return "{$class} '{$this->name}'";
        }
        
        return "{$class} #{$this->id}";
    }

    /**
     * Get audit logs for this model
     */
    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable')->latest();
    }
}

// Made with Bob
