<?php

namespace App\Support;

use App\Models\AdminUser;
use App\Models\Notification;
use App\Models\Team;

class WorkNotification
{
    public static function notifyEmployeeAssignment(
        ?int $employeeId,
        string $title,
        string $message,
        string $type,
        int $relatedId,
        string $relatedType,
        string $priority = 'normal',
        ?string $actionUrl = null
    ): void {
        if (!$employeeId) {
            return;
        }

        $assignedUser = AdminUser::where('employee_id', $employeeId)
            ->where('is_active', true)
            ->first();

        if (!$assignedUser) {
            return;
        }

        Notification::create([
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'priority' => $priority,
            'related_id' => $relatedId,
            'related_type' => $relatedType,
            'action_url' => $actionUrl,
            'recipient_user_id' => $assignedUser->id,
        ]);
    }

    public static function notifyTeamLeaderAssignment(
        ?int $teamId,
        string $title,
        string $message,
        string $type,
        int $relatedId,
        string $relatedType,
        string $priority = 'normal',
        ?string $actionUrl = null
    ): void {
        if (!$teamId) {
            return;
        }

        $team = Team::find($teamId);

        if (!$team?->leader_id) {
            return;
        }

        self::notifyEmployeeAssignment($team->leader_id, $title, $message, $type, $relatedId, $relatedType, $priority, $actionUrl);
    }

    public static function notifyManagers(
        string $title,
        string $message,
        string $type,
        int $relatedId,
        string $relatedType,
        string $priority = 'normal',
        ?string $actionUrl = null
    ): void {
        $managers = AdminUser::where('role', 'manager')
            ->where('is_active', true)
            ->get();

        foreach ($managers as $manager) {
            Notification::create([
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'priority' => $priority,
                'related_id' => $relatedId,
                'related_type' => $relatedType,
                'action_url' => $actionUrl,
                'recipient_user_id' => $manager->id,
            ]);
        }
    }

    /**
     * Create a notification with full control
     */
    public static function create(
        string $title,
        string $message,
        string $type,
        int $relatedId,
        string $relatedType,
        ?int $recipientUserId = null,
        string $priority = 'normal',
        ?string $actionUrl = null
    ): void {
        Notification::create([
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'priority' => $priority,
            'related_id' => $relatedId,
            'related_type' => $relatedType,
            'action_url' => $actionUrl,
            'recipient_user_id' => $recipientUserId,
        ]);
    }
}
