<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Industry;
use App\Models\Office;
use App\Models\Province;
use App\Models\Regency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class OfficeController extends Controller
{
    public function index(Request $request)
    {
        $query = Auth::user()->offices()
            ->with(['regency.province', 'industries'])
            ->withCount('interactions');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $offices = $query->latest()->paginate(10)->withQueryString();

        $user = Auth::user();

        $stats = [
            'offices' => $user->offices()->count(),
            'reviews' => $user->interactions()->count(),
            'approved' => $user->offices()->where('status', 'approved')->count(),
            'pending'  => $user->offices()->where('status', 'pending')->count(),
            'rejected'  => $user->offices()->where('status', 'rejected')->count(),
        ];

        return Inertia::render('Dashboard/Offices/Index', [
            'offices' => $offices->through(fn ($office) => [
                'id' => $office->id,
                'ulid' => $office->ulid,
                'name' => $office->name,
                'slug' => $office->slug,
                'address' => $office->address,
                'status' => $office->status,
                'status_label' => $office->getStatusLabel(),
                'regency' => $office->regency?->name,
                'province' => $office->province?->name,
                'industries' => $office->industries->map(fn ($i) => [
                    'id' => $i->id,
                    'name' => $i->name,
                ]),
                'reviews_count' => $office->reviews_count,
                
            ]),
            'stats' => $stats,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create()
    {
        $provinces = Province::orderBy('name')->get(['id', 'name']);

        $industries = Industry::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get(['id', 'name']);

        return Inertia::render('Dashboard/Offices/Create', [
            'provinces' => $provinces,
            'industries' => $industries,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'industries'   => ['required', 'array', 'min:1', 'max:3'],
            'industries.*' => ['exists:industries,id'],
            'province_id'  => ['required', 'exists:provinces,id'],
            'regency_id'   => ['required', 'exists:regencies,id'],
            'address'      => ['required', 'string'],
            'photos'       => ['required', 'array', 'min:1', 'max:10'],
            'photos.*'     => ['image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        DB::transaction(function () use ($request, $validated) {
            $office = Auth::user()->offices()->create([
                'name'        => $validated['name'],
                'province_id' => $validated['province_id'],
                'regency_id'  => $validated['regency_id'],
                'address'     => $validated['address'],
                'status'      => 'pending',
            ]);

            $office->industries()->sync($validated['industries']);

            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('photos', 'public');

                $file = File::create([
                    'disk'        => 'public',
                    'path'        => $path,
                    'filename'    => $photo->getClientOriginalName(),
                    'mime_type'   => $photo->getMimeType(),
                    'size'        => $photo->getSize(),
                    'uploaded_by' => Auth::id(),
                ]);

                $office->files()->attach($file->id, [
                    'collection' => 'office_photos',
                ]);
            }
        });

        return redirect()
            ->route('kantor.index')
            ->with('success', 'Kantor berhasil diajukan dan menunggu persetujuan.');
    }


    public function show(Office $office)
    {
        // dd($office);
        
        abort_if($office->created_by != auth()->user()->id, 403);

        $office->load([
            'regency.province',
            'industries',
            'files' => fn ($q) => $q->wherePivot('collection', 'office_photos'),
        ]);

        return Inertia::render('Dashboard/Offices/Show', [
            'office' => [
                'id' => $office->id,
                'name' => $office->name,
                'address' => $office->address,
                'slug' => $office->slug,

                'status'          => $office->status,
                'reviewed_by'     => $office->reviewer?->name,
                'reviewed_at'     => $office->reviewed_at,
                'rejection_reason' => $office->rejection_reason,
                'created_at'      => $office->created_at->format('d M Y'),

                'regency' => $office->regency?->name,
                'province' => $office->regency?->province?->name,

                'industries' => $office->industries->map(fn ($i) => [
                    'id' => $i->id,
                    'name' => $i->name,
                ]),

                'photos' => $office->files->map(fn ($file) => [
                    'id' => $file->id,
                    'url' => asset('storage/' . $file->path),
                ]),
            ],
        ]);
    }

    public function edit(Office $office)
    {
        abort_if($office->created_by !== auth()->id(), 403);
        abort_if($office->status === 'approved', 403, 'Kantor yang sudah approved tidak bisa diedit.');

        $office->load([
            'regency.province',
            'industries',
            'files' => fn ($q) => $q->wherePivot('collection', 'office_photos'),
        ]);

        $provinces = Province::orderBy('name')->get(['id', 'name']);

        $regencies = Regency::where('province_id', $office->province_id)
            ->orderBy('name')
            ->get(['id', 'name']);

        $industries = Industry::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get(['id', 'name']);

        return Inertia::render('Dashboard/Offices/Edit', [
            'office' => [
                'id' => $office->id,
                'slug' => $office->slug,
                'name' => $office->name,

                'status'          => $office->status,
                'reviewed_by'     => $office->reviewer?->name,
                'reviewed_at'     => $office->reviewed_at,
                'rejection_reason' => $office->rejection_reason,
                'created_at'      => $office->created_at->format('d M Y'),

                'province_id' => $office->province_id,
                'regency_id' => $office->regency_id,
                'address' => $office->address,
                'status' => $office->status,
                'industries' => $office->industries->pluck('id')->values(),
                'photos' => $office->files->map(fn ($file) => [
                    'id' => $file->id,
                    'url' => asset('storage/' . $file->path),
                    'filename' => $file->filename,
                ]),
            ],
            'provinces' => $provinces,
            'regencies' => $regencies,
            'industries' => $industries,
        ]);
    }

    public function update(Request $request, $slug)
    {
        $office = Office::where('slug', $slug)->firstOrFail();

        abort_if($office->created_by !== auth()->id(), 403);
        abort_if($office->status === 'approved', 403, 'Kantor yang sudah approved tidak bisa diedit.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'industries' => ['required', 'array', 'min:1', 'max:3'],
            'industries.*' => ['exists:industries,id'],
            'province_id' => ['required', 'exists:provinces,id'],
            'regency_id' => ['required', 'exists:regencies,id'],
            'address' => ['required', 'string'],
            'photos' => ['nullable', 'array', 'max:10'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'deleted_photos' => ['nullable', 'array'],
            'deleted_photos.*' => ['exists:files,id'],
        ]);

        DB::transaction(function () use ($request, $validated, $office) {
            $office->update([
                'name' => $validated['name'],
                'slug'        => Office::generateSlug($validated['name'], $office->id),
                'province_id' => $validated['province_id'],
                'regency_id' => $validated['regency_id'],
                'address' => $validated['address'],
                'status' => 'pending',
            ]);

            $office->industries()->sync($validated['industries']);

            if (! empty($validated['deleted_photos'])) {
                $files = File::whereIn('id', $validated['deleted_photos'])->get();

                foreach ($files as $file) {
                    Storage::disk('public')->delete($file->path);
                    $office->files()->detach($file->id);
                    $file->delete();
                }
            }

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $photo) {
                    $path = $photo->store('photos', 'public');

                    $file = File::create([
                        'disk' => 'public',
                        'path' => $path,
                        'filename' => $photo->getClientOriginalName(),
                        'mime_type' => $photo->getMimeType(),
                        'size' => $photo->getSize(),
                        'uploaded_by' => auth()->id(),
                    ]);

                    $office->files()->attach($file->id, [
                        'collection' => 'office_photos',
                    ]);
                }
            }
        });

        return redirect()
            ->route('kantor.index')
            ->with('success', 'Kantor berhasil diperbarui dan menunggu persetujuan ulang.');
    }

    public function destroy($id)
    {
        $office = Office::findOrFail($id);
        $office->delete();

        return back();
    }
}
