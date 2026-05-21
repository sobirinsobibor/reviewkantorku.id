<?php

namespace App\Http\Controllers;

use App\Models\ContentForm;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                'reply'         => ['id' => $templates->get('reply')?->id,          'schema' => $templates->get('reply')?->schema ?? []],
            ],
        ]);
    }

    
}
