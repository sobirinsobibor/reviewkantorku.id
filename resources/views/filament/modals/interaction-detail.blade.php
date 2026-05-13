{{-- Attributes --}}
@php
    $attributes = is_string($record->attributes) 
        ? json_decode($record->attributes, true) 
        : ($record->attributes ?? []);
@endphp
<div style="max-height:75vh; overflow-y:auto; padding:20px;">
    {{-- Header: Nama user + waktu --}}
<div style="display:flex; align-items:center; gap:10px; margin-bottom:16px; padding-bottom:16px; border-bottom:1px solid #f3f4f6;">
    <div style="width:36px; height:36px; border-radius:50%; background:#eff6ff; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:600; color:#1d4ed8; flex-shrink:0;">
        {{ strtoupper(substr($record->user->name ?? 'U', 0, 1)) }}{{ strtoupper(substr(explode(' ', $record->user->name ?? 'U')[1] ?? '', 0, 1)) }}
    </div>
    <div>
        <p style="margin:0; font-size:13px; font-weight:600; color:#111827;">{{ $record->user->name ?? '-' }}</p>
        <p style="margin:0; font-size:11px; color:#9ca3af;">{{ $record->created_at?->format('d M Y H:i') ?? '-' }}</p>
    </div>
</div>

{{-- Experience --}}
@if(!empty($record->experience))
<div style="margin-bottom:16px;">
    <p style="margin:0 0 4px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#9ca3af;">Pengalaman</p>
    <p style="margin:0; font-size:13px; line-height:1.6; color:#374151;">{{ $record->experience }}</p>
</div>
@endif

{{-- Positive Notes --}}
@if(!empty($record->positive_notes))
<div style="margin-bottom:16px;">
    <p style="margin:0 0 4px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#9ca3af;">Hal Positif</p>
    <div style="background:#eff6ff; border-radius:10px; padding:10px 14px;">
        <p style="margin:0; font-size:13px; line-height:1.6; color:#1d4ed8;">{{ $record->positive_notes }}</p>
    </div>
</div>
@endif

@if(count($attributes ?? []))
<div style="display:flex; flex-direction:column; gap:12px;">
    <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:12px;">
        @foreach($attributes as $field)
        @php
            $field      = (object) $field;
            $userData   = array_map('strval', $field->userData ?? []);
            $values     = $field->values ?? [];
            $hasOptions = count($values) > 0;
        @endphp
        <div style="border-radius:16px; border:1px solid #f0f0f0; background:#fafafa; padding:12px;">

            {{-- Label --}}
            <p style="margin:0 0 6px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#9ca3af;">
                {{ $field->label }}
            </p>

            {{-- Description --}}
            @isset($field->description)
            <p style="margin:0 0 6px; font-size:10px; color:#9ca3af;">{{ $field->description }}</p>
            @endisset

            {{-- Options --}}
            @if($hasOptions)
            <div style="display:flex; flex-wrap:wrap; gap:6px;">
                @foreach($values as $option)
                @php
                    $option     = (object) $option;
                    $isSelected = in_array(strval($option->value), $userData);
                @endphp
                <span style="
                    border-radius:999px;
                    padding:3px 10px;
                    font-size:11px;
                    font-weight:500;
                    {{ $isSelected 
                        ? 'background:#2563eb; color:#fff; box-shadow:0 1px 2px rgba(0,0,0,.15);' 
                        : 'background:#fff; color:#9ca3af; border:1px solid #e5e7eb;' }}
                ">{{ $option->label }}</span>
                @endforeach

                @if(!empty($field->other))
                <span style="border-radius:999px; padding:3px 10px; font-size:11px; font-weight:500; background:#fff; color:#d1d5db; border:1px solid #e5e7eb;">
                    Lainnya
                </span>
                @endif
            </div>

            {{-- Free text --}}
            @else
            <div style="border-radius:10px; background:#fff; padding:6px 12px; font-size:13px; font-weight:500; color:#374151;">
                @if(count($userData) && $userData[0] !== '')
                    {{ implode(', ', $userData) }}
                @else
                    <span style="color:#d1d5db;">Tidak diisi</span>
                @endif
            </div>
            @endif

        </div>
        @endforeach
    </div>
</div>
@endif
</div>

