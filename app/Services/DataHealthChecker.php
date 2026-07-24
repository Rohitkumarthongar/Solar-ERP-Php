<?php

namespace App\Services;

use App\Models\Installation;
use App\Models\SiteVisit;
use App\Models\ServiceRequest;
use App\Models\Employee;
use App\Models\Role;
use App\Models\AdminUser;
use App\Models\TaskPayment;
use Illuminate\Support\Collection;

class DataHealthChecker
{
    /**
     * Run all health checks and return issues
     */
    public function runAllChecks(): array
    {
        return [
            'installations_without_team' => $this->checkInstallationsWithoutTeam(),
            'completed_work_without_payment' => $this->checkCompletedWorkWithoutPayment(),
            'employees_without_user' => $this->checkEmployeesWithoutUser(),
            'roles_without_permissions' => $this->checkRolesWithoutPermissions(),
            'orphaned_records' => $this->checkOrphanedRecords(),
            'missing_required_data' => $this->checkMissingRequiredData(),
        ];
    }

    /**
     * Get summary of all issues
     */
    public function getSummary(): array
    {
        $checks = $this->runAllChecks();
        
        return [
            'total_issues' => collect($checks)->sum(fn($items) => count($items)),
            'critical_issues' => $this->countCriticalIssues($checks),
            'warnings' => $this->countWarnings($checks),
            'checks' => $checks,
        ];
    }

    /**
     * Check installations without assigned team
     */
    public function checkInstallationsWithoutTeam(): Collection
    {
        return Installation::whereNull('assigned_team_id')
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->with('customer')
            ->get()
            ->map(function ($installation) {
                return [
                    'type' => 'installation_without_team',
                    'severity' => 'critical',
                    'id' => $installation->id,
                    'number' => $installation->installation_number,
                    'customer' => $installation->customer->name ?? 'N/A',
                    'status' => $installation->status,
                    'scheduled_date' => $installation->scheduled_date?->format('Y-m-d'),
                    'message' => "Installation {$installation->installation_number} has no team assigned",
                    'action' => 'Assign a team to this installation',
                    'link' => route('admin.installations.edit', $installation->id),
                ];
            });
    }

    /**
     * Check completed work without payment records
     */
    public function checkCompletedWorkWithoutPayment(): Collection
    {
        $issues = collect();

        // Check completed installations
        $installations = Installation::where('status', 'completed')
            ->whereDoesntHave('taskPayments')
            ->with('customer', 'team')
            ->get();

        foreach ($installations as $installation) {
            $issues->push([
                'type' => 'completed_work_without_payment',
                'severity' => 'high',
                'module' => 'installation',
                'id' => $installation->id,
                'number' => $installation->installation_number,
                'customer' => $installation->customer->name ?? 'N/A',
                'team' => $installation->team->name ?? 'N/A',
                'completed_date' => $installation->completion_date?->format('Y-m-d'),
                'message' => "Completed installation {$installation->installation_number} has no payment record",
                'action' => 'Generate payment for this installation',
                'link' => route('admin.installations.show', $installation->id),
            ]);
        }

        // Check completed site visits
        $siteVisits = SiteVisit::where('status', 'completed')
            ->whereDoesntHave('taskPayments')
            ->with('customer', 'team')
            ->get();

        foreach ($siteVisits as $visit) {
            $issues->push([
                'type' => 'completed_work_without_payment',
                'severity' => 'high',
                'module' => 'site_visit',
                'id' => $visit->id,
                'number' => $visit->visit_number,
                'customer' => $visit->customer->name ?? 'N/A',
                'team' => $visit->team->name ?? 'N/A',
                'completed_date' => $visit->completed_at?->format('Y-m-d'),
                'message' => "Completed site visit {$visit->visit_number} has no payment record",
                'action' => 'Generate payment for this site visit',
                'link' => route('admin.site-visits.show', $visit->id),
            ]);
        }

        // Check resolved services
        $services = ServiceRequest::where('status', 'resolved')
            ->whereDoesntHave('taskPayments')
            ->with('customer', 'team')
            ->get();

        foreach ($services as $service) {
            $issues->push([
                'type' => 'completed_work_without_payment',
                'severity' => 'medium',
                'module' => 'service',
                'id' => $service->id,
                'number' => $service->ticket_number,
                'customer' => $service->customer->name ?? 'N/A',
                'team' => $service->team->name ?? 'N/A',
                'resolved_date' => $service->resolved_at?->format('Y-m-d'),
                'message' => "Resolved service {$service->ticket_number} has no payment record",
                'action' => 'Generate payment for this service',
                'link' => route('admin.services.show', $service->id),
            ]);
        }

        return $issues;
    }

    /**
     * Check employees without linked user accounts
     */
    public function checkEmployeesWithoutUser(): Collection
    {
        return Employee::whereDoesntHave('user')
            ->where('status', 'active')
            ->get()
            ->map(function ($employee) {
                return [
                    'type' => 'employee_without_user',
                    'severity' => 'high',
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'email' => $employee->email,
                    'phone' => $employee->phone,
                    'role' => $employee->role,
                    'message' => "Employee {$employee->name} has no user account for system access",
                    'action' => 'Create a user account for this employee',
                    'link' => route('admin.employees.edit', $employee->id),
                ];
            });
    }

    /**
     * Check roles without any permissions
     */
    public function checkRolesWithoutPermissions(): Collection
    {
        return Role::all()
            ->filter(function ($role) {
                $permissions = json_decode($role->permissions, true) ?? [];
                return empty($permissions) || count(array_filter($permissions)) === 0;
            })
            ->map(function ($role) {
                $userCount = AdminUser::where('role_id', $role->id)->count();
                return [
                    'type' => 'role_without_permissions',
                    'severity' => 'medium',
                    'id' => $role->id,
                    'name' => $role->name,
                    'user_count' => $userCount,
                    'message' => "Role '{$role->name}' has no permissions assigned ({$userCount} users affected)",
                    'action' => 'Assign permissions to this role',
                    'link' => route('admin.roles.edit', $role->id),
                ];
            })
            ->values();
    }

    /**
     * Check for orphaned records (references to deleted items)
     */
    public function checkOrphanedRecords(): Collection
    {
        $issues = collect();

        // Installations with deleted customers
        $orphanedInstallations = Installation::whereDoesntHave('customer')->count();
        if ($orphanedInstallations > 0) {
            $issues->push([
                'type' => 'orphaned_records',
                'severity' => 'high',
                'module' => 'installations',
                'count' => $orphanedInstallations,
                'message' => "{$orphanedInstallations} installation(s) reference deleted customers",
                'action' => 'Review and clean up orphaned installations',
                'link' => route('admin.installations.index'),
            ]);
        }

        // Site visits with deleted leads
        $orphanedVisits = SiteVisit::whereNotNull('lead_id')
            ->whereDoesntHave('lead')
            ->count();
        if ($orphanedVisits > 0) {
            $issues->push([
                'type' => 'orphaned_records',
                'severity' => 'medium',
                'module' => 'site_visits',
                'count' => $orphanedVisits,
                'message' => "{$orphanedVisits} site visit(s) reference deleted leads",
                'action' => 'Review and clean up orphaned site visits',
                'link' => route('admin.site-visits.index'),
            ]);
        }

        // Services with deleted installations
        $orphanedServices = ServiceRequest::whereNotNull('installation_id')
            ->whereDoesntHave('installation')
            ->count();
        if ($orphanedServices > 0) {
            $issues->push([
                'type' => 'orphaned_records',
                'severity' => 'medium',
                'module' => 'services',
                'count' => $orphanedServices,
                'message' => "{$orphanedServices} service(s) reference deleted installations",
                'action' => 'Review and clean up orphaned services',
                'link' => route('admin.services.index'),
            ]);
        }

        return $issues;
    }

    /**
     * Check for missing required data
     */
    public function checkMissingRequiredData(): Collection
    {
        $issues = collect();

        // Installations without system size
        $noSizeCount = Installation::whereNull('system_size_kw')
            ->orWhere('system_size_kw', 0)
            ->whereIn('status', ['scheduled', 'in_progress', 'completed'])
            ->count();
        if ($noSizeCount > 0) {
            $issues->push([
                'type' => 'missing_required_data',
                'severity' => 'medium',
                'module' => 'installations',
                'field' => 'system_size_kw',
                'count' => $noSizeCount,
                'message' => "{$noSizeCount} installation(s) missing system size",
                'action' => 'Add system size to installations',
                'link' => route('admin.installations.index'),
            ]);
        }

        // Employees without email
        $noEmailCount = Employee::where('status', 'active')
            ->where(function($q) {
                $q->whereNull('email')->orWhere('email', '');
            })
            ->count();
        if ($noEmailCount > 0) {
            $issues->push([
                'type' => 'missing_required_data',
                'severity' => 'low',
                'module' => 'employees',
                'field' => 'email',
                'count' => $noEmailCount,
                'message' => "{$noEmailCount} active employee(s) missing email address",
                'action' => 'Add email addresses to employees',
                'link' => route('admin.employees.index'),
            ]);
        }

        // Employees without phone
        $noPhoneCount = Employee::where('status', 'active')
            ->where(function($q) {
                $q->whereNull('phone')->orWhere('phone', '');
            })
            ->count();
        if ($noPhoneCount > 0) {
            $issues->push([
                'type' => 'missing_required_data',
                'severity' => 'low',
                'module' => 'employees',
                'field' => 'phone',
                'count' => $noPhoneCount,
                'message' => "{$noPhoneCount} active employee(s) missing phone number",
                'action' => 'Add phone numbers to employees',
                'link' => route('admin.employees.index'),
            ]);
        }

        return $issues;
    }

    /**
     * Count critical issues
     */
    private function countCriticalIssues(array $checks): int
    {
        $count = 0;
        foreach ($checks as $items) {
            foreach ($items as $item) {
                if (($item['severity'] ?? '') === 'critical') {
                    $count++;
                }
            }
        }
        return $count;
    }

    /**
     * Count warnings
     */
    private function countWarnings(array $checks): int
    {
        $count = 0;
        foreach ($checks as $items) {
            foreach ($items as $item) {
                if (in_array($item['severity'] ?? '', ['high', 'medium'])) {
                    $count++;
                }
            }
        }
        return $count;
    }

    /**
     * Get severity badge class
     */
    public static function getSeverityBadge(string $severity): string
    {
        return match($severity) {
            'critical' => 'bg-red-100 text-red-800',
            'high' => 'bg-orange-100 text-orange-800',
            'medium' => 'bg-yellow-100 text-yellow-800',
            'low' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get severity icon
     */
    public static function getSeverityIcon(string $severity): string
    {
        return match($severity) {
            'critical' => 'fa-exclamation-circle',
            'high' => 'fa-exclamation-triangle',
            'medium' => 'fa-info-circle',
            'low' => 'fa-lightbulb',
            default => 'fa-question-circle',
        };
    }
}

// Made with Bob
