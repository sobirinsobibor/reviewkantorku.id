<?php

namespace App\Http\Controllers;

use App\Models\ContentForm;
use App\Models\File;
use App\Models\Interaction;
use App\Models\Office;
use Illuminate\Http\Request;
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
}
