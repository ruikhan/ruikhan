<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Notifications\DocumentStatusUpdated;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function approve(Request $request, $id)
    {
        $document = Document::findOrFail($id);
        $document->status = 'approved';
        $document->save();

        // Send notification to user
        $document->user->notify(
            new DocumentStatusUpdated($document, 'approved')
        );

        return redirect()->back()->with('success', 'Document approved!');
    }

    public function reject(Request $request, $id)
    {
        $document = Document::findOrFail($id);
        $document->status = 'rejected';
        $document->rejection_reason = $request->reason;
        $document->save();

        // Send notification to user
        $document->user->notify(
            new DocumentStatusUpdated($document, 'rejected')
        );

        return redirect()->back()->with('success', 'Document rejected.');
    }
}