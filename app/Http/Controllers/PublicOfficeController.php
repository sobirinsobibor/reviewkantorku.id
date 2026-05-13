<?php

namespace App\Http\Controllers;

use App\Models\ContentForm;
use App\Models\Office;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PublicOfficeController extends Controller
{
    public function index()
    {
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
                ];
            });

        return Inertia::render('Public/Offices/Index', [
            'offices' => $offices,
        ]);
    }

    // OfficeController.php

    public function show(Office $office)
    {
        abort_if($office->status !== 'approved', 404);

        $office->load([
            'province',
            'regency',
            'industries',
            'files' => fn ($q) => $q->wherePivot('collection', 'office_photos'),
        ]);

        // Ambil semua template sekaligus, keyed by type
        $templates = ContentForm::query()
            ->whereIn('type', ['review', 'qna', 'cerita_magang', 'menfess'])
            ->where('is_active', true)
            ->orderByDesc('version')
            ->get()
            ->keyBy('type');


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
                'industries'   => $office->industries->map(fn ($i) => [
                    'id'   => $i->id,
                    'name' => $i->name,
                ]),
                'photos' => $office->files->map(fn ($f) => [
                    'id'  => $f->id,
                    'url' => asset('storage/' . $f->path),
                ]),

                // Hitung saja, bukan load semua
                'counts' => [
                    'review'      => $office->interactions()->where('type', 'review')->where('is_hidden', false)->count(),
                    'qna'         => $office->interactions()->where('type', 'qna')->where('is_hidden', false)->count(),
                    'cerita_magang'  => $office->interactions()->where('type', 'cerita_magang')->where('is_hidden', false)->count(),
                    'menfess'     => $office->interactions()->where('type', 'menfess')->where('is_hidden', false)->count(),
                ],
            ],

            'templates' => [
                'review'        => ['id' => $templates->get('review')?->id,         'schema' => $templates->get('review')?->schema ?? []],
                'qna'           => ['id' => $templates->get('qna')?->id,            'schema' => $templates->get('qna')?->schema ?? []],
                'cerita_magang' => ['id' => $templates->get('cerita_magang')?->id,  'schema' => $templates->get('cerita_magang')?->schema ?? []],
                'menfess'       => ['id' => $templates->get('menfess')?->id,        'schema' => $templates->get('menfess')?->schema ?? []],
            ],
        ]);
    }

    // Endpoint lazy-load per tab
    public function feed(Office $office, Request $request)
    {
        abort_if($office->status !== 'approved', 404);

        $type = $request->query('type', 'review');
        abort_unless(in_array($type, ['review', 'qna', 'cerita_magang', 'menfess']), 422);

        $interactions = $office->interactions()
            ->where('type', $type)
            ->where('is_hidden', false)
            ->with([
                'user',
                'files' => fn ($q) => $q->wherePivot('collection', 'review_files'),
            ])
            ->latest()
            ->paginate(10);

        return response()->json([
            'data' => $interactions->getCollection()->map(fn ($interaction) => [
                'id'              => $interaction->id,
                'type'            => $interaction->type,
                'main_contents'    => $interaction->main_contents, // ← ganti ini
                'attributes'      => $interaction->attributes,
                // 'experience'      => $review->experience,
                // 'positive_notes'  => $review->positive_notes,
                'is_anonymous'    => $interaction->is_anonymous,
                'created_at_human'=> $interaction->created_at->format('d M Y H:i'),
                'files'           => $interaction->files->map(fn ($f) => [
                    'id'         => $f->id,
                    'url'        => asset('storage/' . $f->path),
                    'path'       => $f->path,
                    'filename'   => $f->filename,
                    'collection' => $f->pivot?->collection,
                ]),
                'user' => [
                    'name'     => $interaction->is_anonymous ? 'Anonim' : $interaction->user?->name,
                    'initials' => $interaction->is_anonymous
                        ? 'AN'
                        : strtoupper(substr($interaction->user?->name ?? 'User', 0, 2)),
                ],
            ]),
            'meta' => [
                'current_page' => $interactions->currentPage(),
                'last_page'    => $interactions->lastPage(),
                'total'        => $interactions->total(),
            ],
        ]);
    }
}
