@extends('layouts.admin')

@section('title', 'Data Health Check')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2">Data Health Check</h1>
            <p class="text-muted">Monitor data integrity and catch configuration issues early</p>
        </div>
        <div>
            <button onclick="refreshChecks()" class="btn btn-outline-primary me-2">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
            <a href="{{ route('admin.data-health.export') }}" class="btn btn-outline-secondary">
                <i class="fas fa-download"></i> Export Report
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Critical Issues
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $criticalIssues }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Warnings
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $warnings }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Issues
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalIssues }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($totalIssues === 0)
        <!-- All Clear Message -->
        <div class="card shadow mb-4">
            <div class="card-body text-center py-5">
                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                <h3 class="mt-3 text-success">All Systems Healthy!</h3>
                <p class="text-muted">No data integrity issues detected. Your system is running smoothly.</p>
            </div>
        </div>
    @else
        <!-- Issues by Category -->
        @foreach($checks as $checkName => $issues)
            @if(count($issues) > 0)
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            {{ ucwords(str_replace('_', ' ', $checkName)) }}
                        </h6>
                        <span class="badge badge-danger">{{ count($issues) }} issue(s)</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="10%">Severity</th>
                                        <th width="15%">Reference</th>
                                        <th width="40%">Issue</th>
                                        <th width="25%">Action Required</th>
                                        <th width="10%">Link</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($issues as $issue)
                                        <tr>
                                            <td>
                                                <span class="badge {{ \App\Services\DataHealthChecker::getSeverityBadge($issue['severity']) }}">
                                                    <i class="fas {{ \App\Services\DataHealthChecker::getSeverityIcon($issue['severity']) }}"></i>
                                                    {{ ucfirst($issue['severity']) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if(isset($issue['number']))
                                                    <strong>{{ $issue['number'] }}</strong>
                                                @elseif(isset($issue['name']))
                                                    <strong>{{ $issue['name'] }}</strong>
                                                @elseif(isset($issue['module']))
                                                    <span class="text-muted">{{ ucfirst($issue['module']) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                                
                                                @if(isset($issue['customer']))
                                                    <br><small class="text-muted">{{ $issue['customer'] }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $issue['message'] }}
                                                
                                                @if(isset($issue['count']))
                                                    <br><small class="text-muted">Affected records: {{ $issue['count'] }}</small>
                                                @endif
                                                
                                                @if(isset($issue['scheduled_date']))
                                                    <br><small class="text-muted">Scheduled: {{ $issue['scheduled_date'] }}</small>
                                                @endif
                                                
                                                @if(isset($issue['completed_date']))
                                                    <br><small class="text-muted">Completed: {{ $issue['completed_date'] }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $issue['action'] }}</small>
                                            </td>
                                            <td>
                                                @if(isset($issue['link']))
                                                    <a href="{{ $issue['link'] }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-external-link-alt"></i> Fix
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endif

    <!-- Help Section -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-info-circle"></i> About Data Health Checks
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="font-weight-bold">What are Data Health Checks?</h6>
                    <p class="text-muted small">
                        Data health checks automatically scan your system for common configuration issues, 
                        missing data, and inconsistencies that could affect operations. Regular monitoring 
                        helps maintain data integrity and prevents operational problems.
                    </p>
                </div>
                <div class="col-md-6">
                    <h6 class="font-weight-bold">Severity Levels</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2">
                            <span class="badge bg-red-100 text-red-800">Critical</span> 
                            - Requires immediate attention, may block operations
                        </li>
                        <li class="mb-2">
                            <span class="badge bg-orange-100 text-orange-800">High</span> 
                            - Important issues that should be resolved soon
                        </li>
                        <li class="mb-2">
                            <span class="badge bg-yellow-100 text-yellow-800">Medium</span> 
                            - Minor issues that should be addressed
                        </li>
                        <li class="mb-2">
                            <span class="badge bg-blue-100 text-blue-800">Low</span> 
                            - Informational, can be addressed when convenient
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function refreshChecks() {
    const btn = event.target.closest('button');
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';
    
    fetch('{{ route("admin.data-health.check") }}', {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        location.reload();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to refresh checks');
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    });
}
</script>

<style>
.border-left-danger {
    border-left: 0.25rem solid #e74a3b !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
</style>
@endsection

// Made with Bob
