<?php

namespace App\Http\Controllers;

use App\Models\Interaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InteractionController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $interactions = Interaction::query()
            ->where('user_id', $userId)
            ->with(['office:id,name,slug'])
            ->latest()
            ->paginate(10)
            ->through(fn ($interaction) => [
                'id' => $interaction->id,
                'ulid' => $interaction->ulid,
                'type' => $interaction->type,
                'user_id' => $interaction->user_id,
                'office' => [
                    'name' => $interaction->office?->name,
                    'slug' => $interaction->office?->slug,
                ],
                'attributes' => $interaction->attributes,
                'experience' => $interaction->experience,
                'is_hidden' => $interaction->is_hidden,
                'created_at' => $interaction->created_at->format('d M Y'),
            ]);

        // 🔥 STATS
        $stats = [
            'total' => Interaction::where('user_id', $userId)->count(),
            'interaction' => Interaction::where('user_id', $userId)->where('type', 'review')->count(),
            'cerita_magang' => Interaction::where('user_id', $userId)->where('type', 'cerita_magang')->count(),
            'menfess' => Interaction::where('user_id', $userId)->where('type', 'menfess')->count(),
            'qna' => Interaction::where('user_id', $userId)->where('type', 'qna')->count(),
        ];

        return Inertia::render('Dashboard/Interactions/Index', [
            'interactions' => $interactions,
            'stats' => $stats,
        ]);
    }

    public function update(Request $request)
    {
        $interaction = Interaction::select('is_hidden', 'user_id')->where('ulid', $request->ulid)->firstOrFail();
    
        abort_if($interaction->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'is_hidden' => ['required', 'boolean'],
        ]);

        // dd($validated['is_hidden']);

        $interaction->update([
            'is_hidden' => $validated['is_hidden'],
        ]);

        return back()->with('success', 'Status interaksi berhasil diperbarui.');
    }
}
