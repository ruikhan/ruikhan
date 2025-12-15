<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangayCase; // You will need to create this Model later
use App\Models\ServiceTicket; // You will need to create this Model later

class IssueController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validate the Base Input
        $validated = $request->validate([
            'type' => 'required|in:civil,public',
            'description' => 'required|string',
        ]);

        $user = $request->user();

        if ($request->type === 'civil') {
            // --- ROUTE A: BARANGAY JUSTICE SYSTEM (Disputes) ---
            
            $civilData = $request->validate([
                'respondent_name' => 'required|string',
                'nature_of_dispute' => 'required|string',
            ]);

            // Create the Case
            BarangayCase::create([
                'user_id' => $user->id,
                'barangay_id' => $user->barangay_id ?? 1, // Fallback to 1 if not set
                'respondent_name' => $civilData['respondent_name'],
                'type' => $civilData['nature_of_dispute'],
                'narrative' => $request->description,
                'status' => 'filed',
                'stage' => 'mediation_pending'
            ]);

            return redirect()->back()->with('flash', [
                'success' => 'Case filed! Check the "My Cases" tab for the mediation schedule.'
            ]);

        } else {
            // --- ROUTE B: MUNICIPAL SERVICES (Tickets) ---

            $publicData = $request->validate([
                'department' => 'required|string',
                'location' => 'required|string',
            ]);

            // Create the Ticket
            $ticket = ServiceTicket::create([
                'user_id' => $user->id,
                'department' => $publicData['department'],
                'location' => $publicData['location'],
                'issue' => $request->description,
                'status' => 'open',
                'ticket_number' => 'TKT-' . strtoupper(uniqid())
            ]);

             return redirect()->back()->with('flash', [
                'success' => 'Ticket Created! Track it with ID: ' . $ticket->ticket_number
            ]);
        }
    }
}