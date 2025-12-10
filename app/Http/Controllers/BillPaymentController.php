<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str; // To generate random receipt codes

class BillPaymentController extends Controller
{
    // 1. Show the Payment Form
    public function create()
    {
        return Inertia::render('Bills/Create');
    }

    // 2. Process the "Fake" Payment
    public function store(Request $request)
    {
        $validated = $request->validate([
            'biller_name' => 'required|string',
            'account_number' => 'required|numeric',
            'amount' => 'required|numeric|min:100',
        ]);

        // --- SIMULATE BANK PROCESSING ---
        // We randomly decide if the payment works (80% success rate)
        // In a real app, this is where you call the GCash/Stripe API.
        $isSuccessful = rand(1, 100) <= 80; 

        if (!$isSuccessful) {
            return back()->withErrors(['amount' => 'Payment Gateway Timeout. Please try again.']);
        }

        // Generate a receipt code (e.g., TRN-839281)
        $refCode = 'TRN-' . strtoupper(Str::random(8));

        // Save Record
        $request->user()->billPayments()->create([
            'biller_name' => $validated['biller_name'],
            'account_number' => $validated['account_number'],
            'amount' => $validated['amount'],
            'reference_code' => $refCode,
            'status' => 'successful'
        ]);

        // Redirect to a "Receipt" page (or back with success message)
        return redirect()->route('bills.history')->with('success', "Payment Successful! Ref: $refCode");
    }

    // 3. Show History
    public function index()
    {
        return Inertia::render('Bills/Index', [
            'payments' => auth()->user()->billPayments()->latest()->get()
        ]);
    }
}