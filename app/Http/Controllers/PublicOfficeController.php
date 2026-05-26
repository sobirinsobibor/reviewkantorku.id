<?php

namespace App\Http\Controllers;

use App\Models\ContentForm;
use App\Models\Office;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PublicOfficeController extends Controller
{
    public function index()
    {
        dd('test');
        $offices = Office::query()
            ->where('status', 'approved')
            ->with([
                'province',
                'regency',
                'industries',
                'files' => function ($q) {
                    $q->wherePivot('collection', 'office_photos');
                },
            ])
            ->withCount('interactions')
            ->latest()
            ->paginate(4)
            ->through(function ($office) {
                $photo = $office->files->first();

                return [
                    'id' => $office->id,
                    'name' => $office->name,
                    'slug' => $office->slug,
                    'status' => $office->status,
                    'status_label' => $office->getStatusLabel(),
                    'province' => $office->province?->name,
                    'regency' => $office->regency?->name,
                    'industries' => $office->industries->map(fn ($industry) => [
                        'id' => $industry->id,
                        'name' => $industry->name,
                    ]),
                    'photo_url' => $photo
                        ? asset('storage/' . $photo->path)
                        : null,
                    'interactions_count' => $office->interactions_count,
                    'likes_count' => $office->likes_count
                ];
            });

        return Inertia::render('Public/Offices/Index', [
            'offices' => $offices,
        ]);
    }


    public function show(Office $office)
    {
        abort_if($office->status !== 'approved', 404);

        $office->load([
            'province',
            'regency',
            'industries',
            'files' => fn ($q) => $q->wherePivot('collection', 'office_photos'),
        ]);

        $templates = ContentForm::query()
            ->whereIn('type', ['review', 'qna', 'cerita_magang', 'menfess'])
            ->where('is_active', true)
            ->orderByDesc('version')
            ->get()
            ->keyBy('type');

        $user = auth()->user();

        return Inertia::render('Public/Offices/Show', [
            'office' => [
                'id'           => $office->id,
                'name'         => $office->name,
                'slug'         => $office->slug,
                'address'      => $office->address,
                'status'       => $office->status,
                'status_label' => $office->getStatusLabel(),
                'province'     => $office->province?->name,
                'regency'      => $office->regency?->name,

                'likes_count' => $office->reactions()
                    ->where('type', 'like')
                    ->count(),

                'dislikes_count' => $office->reactions()
                    ->where('type', 'dislike')
                    ->count(),

                'is_liked_by_user' => $user
                    ? $office->reactions()
                        ->where('user_id', $user->id)
                        ->where('type', 'like')
                        ->exists()
                    : false,

                'is_disliked_by_user' => $user
                    ? $office->reactions()
                        ->where('user_id', $user->id)
                        ->where('type', 'dislike')
                        ->exists()
                    : false,

                'industries' => $office->industries->map(fn ($i) => [
                    'id'   => $i->id,
                    'name' => $i->name,
                ]),

                'photos' => $office->files->map(fn ($f) => [
                    'id'  => $f->id,
                    'url' => asset('storage/' . $f->path),
                ]),

                'counts' => [
                    'review' => $office->interactions()
                        ->where('type', 'review')
                        ->where('is_hidden', false)
                        ->count(),

                    'qna' => $office->interactions()
                        ->where('type', 'qna')
                        ->where('is_hidden', false)
                        ->count(),

                    'cerita_magang' => $office->interactions()
                        ->where('type', 'cerita_magang')
                        ->where('is_hidden', false)
                        ->count(),

                    'menfess' => $office->interactions()
                        ->where('type', 'menfess')
                        ->where('is_hidden', false)
                        ->count(),
                ],
            ],

            'templates' => [
                'review' => [
                    'id'     => $templates->get('review')?->id,
                    'schema' => $templates->get('review')?->schema ?? [],
                ],

                'qna' => [
                    'id'     => $templates->get('qna')?->id,
                    'schema' => $templates->get('qna')?->schema ?? [],
                ],

                'cerita_magang' => [
                    'id'     => $templates->get('cerita_magang')?->id,
                    'schema' => $templates->get('cerita_magang')?->schema ?? [],
                ],

                'menfess' => [
                    'id'     => $templates->get('menfess')?->id,
                    'schema' => $templates->get('menfess')?->schema ?? [],
                ],

                'reply' => [
                    'id'     => $templates->get('reply')?->id,
                    'schema' => $templates->get('reply')?->schema ?? [],
                ],
            ],
        ]);
    }

    public function toggleReaction(Request $request, Office $office): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:like,dislike'],
        ]);

        $user = Auth::user();

        $reaction = $office->reactions()
            ->where('user_id', $user->id)
            ->first();

        if ($reaction && $reaction->type === $validated['type']) {
            $reaction->delete();

            return back();
        }

        if ($reaction) {
            $reaction->update([
                'type' => $validated['type'],
            ]);

            return back();
        }

        $office->reactions()->create([
            'user_id' => $user->id,
            'type' => $validated['type'],
        ]);

        return back();
    }
}
