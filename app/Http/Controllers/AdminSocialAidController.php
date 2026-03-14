<?php
// ════════════════════════════════════════════════════════════════
// FILE: app/Http/Controllers/AdminSocialAidController.php
// ════════════════════════════════════════════════════════════════
namespace App\Http\Controllers;

use App\Models\SocialAidApplication;
use App\Notifications\ApplicationStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AdminSocialAidController extends Controller
{
    public function index()
    {
        $applications = SocialAidApplication::with('user:id,name,email')
            ->latest()
            ->paginate(15)
            ->through(fn ($a) => [
                'id'            => $a->id,
                'tracking_code' => $a->tracking_code,
                'program_type'  => $a->program_type,
                'program_title' => $a->program_title,
                'status'        => $a->status,
                'user'          => ['name' => $a->user?->name, 'email' => $a->user?->email],
                'created_at'    => $a->created_at?->toISOString(),
                'release_date'  => $a->release_date?->toDateString(),
            ]);

        return Inertia::render('Admin/SocialAid/Index', compact('applications'));
    }

    public function show($id)
    {
        $application = SocialAidApplication::with('user:id,name,email,phone_number,address')->findOrFail($id);

        return Inertia::render('Admin/SocialAid/Show', [
            'application' => [
                'id'               => $application->id,
                'tracking_code'    => $application->tracking_code,
                'program_type'     => $application->program_type,
                'program_title'    => $application->program_title,
                'status'           => $application->status,
                'application_data' => $application->application_data ?? [],
                'attachments'      => $application->attachments ?? [],
                'approved_amount'  => $application->approved_amount,
                'release_date'     => $application->release_date?->toDateString(),
                'release_venue'    => $application->release_venue,
                'admin_note'       => $application->admin_note,
                'rejection_reason' => $application->rejection_reason,
                'workflow_history' => $application->workflow_history ?? [],
                'created_at'       => $application->created_at?->toISOString(),
            ],
            'user' => [
                'id'           => $application->user->id,
                'name'         => $application->user->name,
                'email'        => $application->user->email,
                'phone_number' => $application->user->phone_number,
                'address'      => $application->user->address,
            ],
        ]);
    }

    public function approve(Request $request, $id)
    {
        try {
            $application = SocialAidApplication::with('user')->findOrFail($id);

            $validated = $request->validate([
                'approved_amount' => 'required|numeric|min:0',
                'release_date'    => 'required|date|after:today',
                'release_venue'   => 'required|string|max:255',
                'admin_note'      => 'nullable|string|max:1000',
            ]);

            $application->addWorkflowEntry('approved', $validated['admin_note']);
            $application->update([
                'status'          => 'approved',
                'approved_amount' => $validated['approved_amount'],
                'release_date'    => $validated['release_date'],
                'release_venue'   => $validated['release_venue'],
                'admin_note'      => $validated['admin_note'],
                'workflow_history'=> $application->workflow_history,
                'reviewed_by'     => auth()->id(),
                'reviewed_at'     => now(),
            ]);

            $application->user?->notify(new ApplicationStatusNotification(
                section:       'social_aid',
                trackingCode:  $application->tracking_code,
                status:        'approved',
                programTitle:  $application->program_title,
                adminNote:     $validated['admin_note'],
                releaseDate:   \Carbon\Carbon::parse($validated['release_date'])->format('F j, Y'),
                releaseVenue:  $validated['release_venue'],
            ));

            return redirect()->back()->with('success', 'Application approved! User has been notified.');

        } catch (\Exception $e) {
            Log::error('Social Aid Approve Error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Failed to approve application.']);
        }
    }

    public function reject(Request $request, $id)
    {
        try {
            $application = SocialAidApplication::with('user')->findOrFail($id);

            $validated = $request->validate([
                'rejection_reason' => 'required|string|max:1000',
            ]);

            $application->addWorkflowEntry('rejected', $validated['rejection_reason']);
            $application->update([
                'status'           => 'rejected',
                'rejection_reason' => $validated['rejection_reason'],
                'workflow_history' => $application->workflow_history,
                'reviewed_by'      => auth()->id(),
                'reviewed_at'      => now(),
            ]);

            $application->user?->notify(new ApplicationStatusNotification(
                section:      'social_aid',
                trackingCode: $application->tracking_code,
                status:       'rejected',
                programTitle: $application->program_title,
                adminNote:    $validated['rejection_reason'],
            ));

            return redirect()->back()->with('success', 'Application rejected. User has been notified.');

        } catch (\Exception $e) {
            Log::error('Social Aid Reject Error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Failed to reject application.']);
        }
    }

    public function markReleased($id)
    {
        try {
            $application = SocialAidApplication::with('user')->findOrFail($id);
            $application->addWorkflowEntry('released', 'Assistance successfully released to applicant.');
            $application->update([
                'status'           => 'released',
                'workflow_history' => $application->workflow_history,
            ]);
            $application->user?->notify(new ApplicationStatusNotification(
                section:      'social_aid',
                trackingCode: $application->tracking_code,
                status:       'released',
                programTitle: $application->program_title,
            ));
            return redirect()->back()->with('success', 'Marked as released.');
        } catch (\Exception $e) {
            Log::error('Social Aid Release Error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Failed to mark as released.']);
        }
    }
}


// ════════════════════════════════════════════════════════════════
// FILE: app/Http/Controllers/AdminHealthController.php
// ════════════════════════════════════════════════════════════════
namespace App\Http\Controllers;

use App\Models\HealthApplication;
use App\Notifications\ApplicationStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AdminHealthController extends Controller
{
    public function index()
    {
        $applications = HealthApplication::with('user:id,name,email')
            ->latest()
            ->paginate(15)
            ->through(fn ($a) => [
                'id'               => $a->id,
                'tracking_code'    => $a->tracking_code,
                'application_type' => $a->application_type,
                'status'           => $a->status,
                'user'             => ['name' => $a->user?->name, 'email' => $a->user?->email],
                'scheduled_at'     => $a->scheduled_at?->toISOString(),
                'assigned_doctor'  => $a->assigned_doctor,
                'created_at'       => $a->created_at?->toISOString(),
            ]);

        return Inertia::render('Admin/Health/Index', compact('applications'));
    }

    public function show($id)
    {
        $application = HealthApplication::with('user:id,name,email,phone_number,address')->findOrFail($id);

        return Inertia::render('Admin/Health/Show', [
            'application' => [
                'id'               => $application->id,
                'tracking_code'    => $application->tracking_code,
                'application_type' => $application->application_type,
                'status'           => $application->status,
                'application_data' => $application->application_data ?? [],
                'attachments'      => $application->attachments ?? [],
                'scheduled_at'     => $application->scheduled_at?->toISOString(),
                'assigned_doctor'  => $application->assigned_doctor,
                'clinic_location'  => $application->clinic_location,
                'assistance_amount'=> $application->assistance_amount,
                'admin_note'       => $application->admin_note,
                'rejection_reason' => $application->rejection_reason,
                'workflow_history' => $application->workflow_history ?? [],
                'created_at'       => $application->created_at?->toISOString(),
            ],
            'user' => [
                'id'           => $application->user->id,
                'name'         => $application->user->name,
                'email'        => $application->user->email,
                'phone_number' => $application->user->phone_number,
                'address'      => $application->user->address,
            ],
        ]);
    }

    public function approve(Request $request, $id)
    {
        try {
            $application = HealthApplication::with('user')->findOrFail($id);

            $validated = $request->validate([
                'scheduled_at'     => 'required|date|after:now',
                'assigned_doctor'  => 'required|string|max:255',
                'clinic_location'  => 'required|string|max:255',
                'assistance_amount'=> 'nullable|numeric|min:0',
                'admin_note'       => 'nullable|string|max:1000',
            ]);

            $application->addWorkflowEntry('scheduled', $validated['admin_note']);
            $application->update([
                'status'           => 'scheduled',
                'scheduled_at'     => $validated['scheduled_at'],
                'assigned_doctor'  => $validated['assigned_doctor'],
                'clinic_location'  => $validated['clinic_location'],
                'assistance_amount'=> $validated['assistance_amount'] ?? null,
                'admin_note'       => $validated['admin_note'],
                'workflow_history' => $application->workflow_history,
                'reviewed_by'      => auth()->id(),
                'reviewed_at'      => now(),
            ]);

            $scheduledDate = \Carbon\Carbon::parse($validated['scheduled_at'])
                ->format('F j, Y \a\t g:i A');

            $application->user?->notify(new ApplicationStatusNotification(
                section:        'health',
                trackingCode:   $application->tracking_code,
                status:         'scheduled',
                programTitle:   $application->application_type,
                adminNote:      $validated['admin_note'],
                scheduledAt:    $scheduledDate,
                assignedDoctor: $validated['assigned_doctor'],
            ));

            return redirect()->back()->with('success', 'Health application approved and appointment scheduled. User notified.');

        } catch (\Exception $e) {
            Log::error('Health Approve Error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Failed to approve health application.']);
        }
    }

    public function reject(Request $request, $id)
    {
        try {
            $application = HealthApplication::with('user')->findOrFail($id);

            $validated = $request->validate([
                'rejection_reason' => 'required|string|max:1000',
            ]);

            $application->addWorkflowEntry('rejected', $validated['rejection_reason']);
            $application->update([
                'status'           => 'rejected',
                'rejection_reason' => $validated['rejection_reason'],
                'workflow_history' => $application->workflow_history,
                'reviewed_by'      => auth()->id(),
                'reviewed_at'      => now(),
            ]);

            $application->user?->notify(new ApplicationStatusNotification(
                section:      'health',
                trackingCode: $application->tracking_code,
                status:       'rejected',
                programTitle: $application->application_type,
                adminNote:    $validated['rejection_reason'],
            ));

            return redirect()->back()->with('success', 'Application rejected. User notified.');

        } catch (\Exception $e) {
            Log::error('Health Reject Error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Failed to reject health application.']);
        }
    }

    public function markCompleted($id)
    {
        $application = HealthApplication::with('user')->findOrFail($id);
        $application->addWorkflowEntry('completed', 'Appointment completed.');
        $application->update(['status' => 'completed', 'workflow_history' => $application->workflow_history]);
        return redirect()->back()->with('success', 'Marked as completed.');
    }
}


// ════════════════════════════════════════════════════════════════
// FILE: app/Http/Controllers/AdminEnvironmentController.php
// ════════════════════════════════════════════════════════════════
namespace App\Http\Controllers;

use App\Models\EnvironmentReport;
use App\Notifications\ApplicationStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AdminEnvironmentController extends Controller
{
    public function index()
    {
        $reports = EnvironmentReport::with('user:id,name,email')
            ->latest()
            ->paginate(15)
            ->through(fn ($r) => [
                'id'           => $r->id,
                'tracking_code'=> $r->tracking_code,
                'report_type'  => $r->report_type,
                'severity'     => $r->severity,
                'location'     => $r->location,
                'status'       => $r->status,
                'user'         => ['name' => $r->user?->name, 'email' => $r->user?->email],
                'created_at'   => $r->created_at?->toISOString(),
            ]);

        return Inertia::render('Admin/Environment/Index', compact('reports'));
    }

    public function show($id)
    {
        $report = EnvironmentReport::with('user:id,name,email,phone_number,address')->findOrFail($id);

        return Inertia::render('Admin/Environment/Show', [
            'report' => [
                'id'               => $report->id,
                'tracking_code'    => $report->tracking_code,
                'report_type'      => $report->report_type,
                'severity'         => $report->severity,
                'location'         => $report->location,
                'latitude'         => $report->latitude,
                'longitude'        => $report->longitude,
                'report_data'      => $report->report_data ?? [],
                'attachments'      => $report->attachments ?? [],
                'status'           => $report->status,
                'admin_note'       => $report->admin_note,
                'action_taken'     => $report->action_taken,
                'resolution_date'  => $report->resolution_date?->toDateString(),
                'workflow_history' => $report->workflow_history ?? [],
                'created_at'       => $report->created_at?->toISOString(),
            ],
            'user' => [
                'id'           => $report->user->id,
                'name'         => $report->user->name,
                'email'        => $report->user->email,
                'phone_number' => $report->user->phone_number,
                'address'      => $report->user->address,
            ],
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $report = EnvironmentReport::with('user')->findOrFail($id);

            $validated = $request->validate([
                'status'          => 'required|in:acknowledged,investigating,resolved,dismissed',
                'admin_note'      => 'nullable|string|max:1000',
                'action_taken'    => 'nullable|string|max:1000',
                'resolution_date' => 'nullable|date',
            ]);

            $report->addWorkflowEntry($validated['status'], $validated['admin_note'] ?? $validated['action_taken']);
            $report->update([
                'status'           => $validated['status'],
                'admin_note'       => $validated['admin_note'],
                'action_taken'     => $validated['action_taken'],
                'resolution_date'  => $validated['resolution_date'],
                'workflow_history' => $report->workflow_history,
                'acknowledged_at'  => $validated['status'] === 'acknowledged' ? now() : $report->acknowledged_at,
            ]);

            $report->user?->notify(new ApplicationStatusNotification(
                section:      'environment',
                trackingCode: $report->tracking_code,
                status:       $validated['status'],
                programTitle: $report->report_type . ' Report',
                adminNote:    $validated['admin_note'],
                actionTaken:  $validated['action_taken'],
            ));

            return redirect()->back()->with('success', 'Report status updated. Reporter has been notified.');

        } catch (\Exception $e) {
            Log::error('Environment Update Error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Failed to update report.']);
        }
    }
}