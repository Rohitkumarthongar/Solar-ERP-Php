<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DataHealthChecker;
use Illuminate\Http\Request;

class DataHealthController extends Controller
{
    protected $healthChecker;

    public function __construct(DataHealthChecker $healthChecker)
    {
        $this->healthChecker = $healthChecker;
    }

    /**
     * Display data health dashboard
     */
    public function index()
    {
        $summary = $this->healthChecker->getSummary();
        
        return view('admin.data-health.index', [
            'summary' => $summary,
            'checks' => $summary['checks'],
            'totalIssues' => $summary['total_issues'],
            'criticalIssues' => $summary['critical_issues'],
            'warnings' => $summary['warnings'],
        ]);
    }

    /**
     * Get health check data as JSON (for AJAX refresh)
     */
    public function check(Request $request)
    {
        $summary = $this->healthChecker->getSummary();
        
        if ($request->wantsJson()) {
            return response()->json($summary);
        }
        
        return redirect()->route('admin.data-health.index');
    }

    /**
     * Export health check report
     */
    public function export()
    {
        $summary = $this->healthChecker->getSummary();
        
        $csv = "Data Health Check Report\n";
        $csv .= "Generated: " . now()->format('Y-m-d H:i:s') . "\n\n";
        $csv .= "Summary\n";
        $csv .= "Total Issues," . $summary['total_issues'] . "\n";
        $csv .= "Critical Issues," . $summary['critical_issues'] . "\n";
        $csv .= "Warnings," . $summary['warnings'] . "\n\n";
        
        foreach ($summary['checks'] as $checkName => $issues) {
            if (count($issues) > 0) {
                $csv .= "\n" . ucwords(str_replace('_', ' ', $checkName)) . "\n";
                $csv .= "Severity,Type,Message,Action\n";
                
                foreach ($issues as $issue) {
                    $csv .= '"' . ($issue['severity'] ?? 'N/A') . '",';
                    $csv .= '"' . ($issue['type'] ?? 'N/A') . '",';
                    $csv .= '"' . ($issue['message'] ?? 'N/A') . '",';
                    $csv .= '"' . ($issue['action'] ?? 'N/A') . '"' . "\n";
                }
            }
        }
        
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="data-health-report-' . now()->format('Y-m-d') . '.csv"');
    }
}

// Made with Bob
