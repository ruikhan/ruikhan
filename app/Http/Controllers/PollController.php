<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\PollOption;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PollController extends Controller
{
    public function index()
    {
        // Load polls with their options and check if user voted
        $polls = Poll::with(['options', 'votes'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($poll) {
                // Calculate total votes for percentage math
                $total = $poll->options->sum('votes_count');
                $poll->total_votes = $total;
                $poll->user_has_voted = $poll->has_voted;
                return $poll;
            });

        return Inertia::render('Polls/Index', [
            'polls' => $polls
        ]);
    }

    public function vote(Request $request)
    {
        $request->validate([
            'poll_id' => 'required|exists:polls,id',
            'option_id' => 'required|exists:poll_options,id',
        ]);

        $user = Auth::user();

        // Security Check: Did they already vote?
        $existingVote = Vote::where('user_id', $user->id)
                            ->where('poll_id', $request->poll_id)
                            ->first();

        if ($existingVote) {
            return redirect()->back()->withErrors(['message' => 'You have already voted!']);
        }

        // 1. Record the vote
        Vote::create([
            'user_id' => $user->id,
            'poll_id' => $request->poll_id,
            'poll_option_id' => $request->option_id
        ]);

        // 2. Increment the counter (for fast analytics)
        $option = PollOption::find($request->option_id);
        $option->increment('votes_count');

        return redirect()->back();
    }

    // ADMIN ONLY: Create a new Poll
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'options' => 'required|array|min:2', // Must have at least 2 choices
            'options.*' => 'required|string'
        ]);

        $poll = Poll::create([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        foreach ($request->options as $optionText) {
            PollOption::create([
                'poll_id' => $poll->id,
                'option_text' => $optionText
            ]);
        }

        return redirect()->back();
    }
}