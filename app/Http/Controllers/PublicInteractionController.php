<?php

namespace App\Http\Controllers;

use App\Models\ContentForm;
use App\Models\File;
use App\Models\Interaction;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PublicInteractionController extends Controller
{
    public function store(Request $request, Office $office)
    {
        // dd($request);
        abort_if($office->status !== 'approved', 404);

        $validated = $request->validate([
            'type' => ['required', 'in:review,cerita_magang,menfess,qna'],
            'content_form_id' => ['nullable', 'exists:content_forms,id'],
            'is_anonymous' => ['nullable', 'boolean'],
            'answers' => ['required', 'array'],
            'files' => ['nullable', 'array', 'max:5'],
            'files.*' => ['image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        DB::transaction(function () use ($request, $office, $validated) {
            $answers = $request->input('answers', []);

            $template = $validated['content_form_id']
                ? ContentForm::find($validated['content_form_id'])
                : null;

            $attributes = [];

            if ($template) {
                $schema = is_string($template->schema)
                    ? json_decode($template->schema, true)
                    : $template->schema;

                $attributes = collect($schema)
                    ->map(function ($field) use ($answers) {
                        if (!isset($field['name'])) {
                            return $field;
                        }

                        $name        = $field['name'];
                        $answer      = $answers[$name] ?? null;
                        $otherAnswer = $answers[$name . '_other'] ?? null;

                        if (is_array($answer)) {
                            $userData = $answer;

                            if (in_array('__other__', $userData) && filled($otherAnswer)) {
                                $userData   = array_values(array_filter($userData, fn($i) => $i !== '__other__'));
                                $userData[] = 'other:' . $otherAnswer;
                            }
                        } else {
                            if ($answer === '__other__' && filled($otherAnswer)) {
                                $userData = ['other:' . $otherAnswer];
                            } elseif (filled($answer)) {
                                $userData = [$answer];
                            } else {
                                $userData = [];
                            }
                        }

                        $field['userData'] = $userData;

                        return $field;
                    })
                    ->values()
                    ->toArray();
            }

            $review = Interaction::create([
                'office_id' => $office->id,
                'user_id' => auth()->id(),
                'ulid' => (string) Str::ulid(),
                'type' => $validated['type'],
                'attributes' => $attributes,
                'is_anonymous' => $request->boolean('is_anonymous'),
            ]);

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $uploadedFile) {
                    $path = $uploadedFile->store('reviews', 'public');

                    $file = File::create([
                        'disk' => 'public',
                        'path' => $path,
                        'filename' => $uploadedFile->getClientOriginalName(),
                        'mime_type' => $uploadedFile->getMimeType(),
                        'size' => $uploadedFile->getSize(),
                        'uploaded_by' => auth()->id(),
                    ]);

                    $review->files()->attach($file->id, [
                        'collection' => $validated['type'] . '_files',
                    ]);
                }
            }
        });

        return back()->with('success', 'Review berhasil dikirim.');
    }

    // Endpoint lazy-load per tab
    public function feed(Office $office, Request $request)
    {
        abort_if($office->status !== 'approved', 404);

        $type = $request->query('type', 'review');
        abort_unless(in_array($type, ['review', 'qna', 'cerita_magang', 'menfess', 'reply']), 422);

        $interactions = $office->interactions()
            ->where('type', $type)
            ->where('is_hidden', false)
            ->with([
                'user',
                'files' => fn ($q) => $q->wherePivot('collection', 'review_files'),
            ])
            ->withExists([
                'likes as is_liked' => fn ($q) =>
                    $q->where('user_id', auth()->id())
            ])
            ->latest()
            ->paginate(10);

        return response()->json([
            'data' => $interactions->getCollection()->map(fn ($interaction) => [
                'id'              => $interaction->id,
                'ulid'            => $interaction->ulid,
                'type'            => $interaction->type,
                'main_contents'   => $interaction->main_contents,
                'attributes'      => $interaction->attributes,
                'is_anonymous'    => $interaction->is_anonymous,
                'created_at_human'=> $interaction->created_at->format('d M Y H:i'),

                'likes_count'     => $interaction->likes_count,
                'is_liked'        => (bool) $interaction->is_liked,

                'files' => $interaction->files->map(fn ($f) => [
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

    public function reply(Request $request, Interaction $interaction)
    {
        $validated = $request->validate([
            'content'      => ['required', 'string', 'max:1000'],
            'is_anonymous' => ['nullable', 'boolean'],
        ]);

        Interaction::create([
            'ulid'         => (string) Str::ulid(),
            'office_id'    => $interaction->office_id,
            'user_id'      => auth()->id(),
            'parent_id'    => $interaction->ulid,
            'type'         => $interaction->type,
            'is_anonymous' => $request->boolean('is_anonymous'),
            'attributes'   => [
                [
                    'name'     => 'reply',
                    'userData' => [$validated['content']],
                ]
            ],
        ]);

        return back()->with('success', 'Balasan berhasil dikirim.');
    }

    public function toggleLike(Interaction $interaction)
    {
        // dd('test');
        $user = Auth::user();
        $interaction->likes()->toggle($user->id);

        return back();
    }
}
