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
                'main_contents' => $interaction->main_contents,
                'office' => [
                    'name' => $interaction->office?->name,
                    'slug' => $interaction->office?->slug,
                ],
                'user' => [
                    'name' => $interaction->user?->name,
                    'initials' => $interaction->is_anonymous
                        ? 'AN'
                        : strtoupper(substr($interaction->user?->name ?? 'User', 0, 2)),
                ],
                'attributes' => $interaction->attributes,
                'experience' => $interaction->experience,
                'is_hidden' => $interaction->is_hidden,
                'is_anonymous' => $interaction->is_anonymous,
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

    public function update(Request $request, $ulid)
    {
        
        $interaction = Interaction::select('id', 'ulid', 'is_hidden', 'is_anonymous', 'user_id')->where('ulid', $ulid)->firstOrFail();
        // dd($interaction);
        abort_if($interaction->user_id !== auth()->id(), 403);

        $validated = $request->validate([
            'is_hidden' => ['sometimes', 'boolean'],
            'is_anonymous' => ['sometimes', 'boolean'],
        ]);

        // dd($validated);

        $interaction->update($validated);

        return back()->with('success', 'Interaksi berhasil diperbarui.');
    }
}
