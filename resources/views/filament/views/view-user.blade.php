<x-filament-panels::page>

    {{ $this->infolist }}

    @php
        $data     = $this->getViewData();
        $record   = $this->getRecord();
        $words    = explode(' ', $record->name ?? '');
        $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1] ?? '', 0, 1));
    @endphp

    <style>
        .stats-grid-5 {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 1px;
            background: #e5e7eb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
        }
        .stats-grid-4 {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1px;
            background: #e5e7eb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
        }
        .stats-grid-3 {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1px;
            background: #e5e7eb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            margin-top: 1px;
        }
        .stat-cell {
            background: #fff;
            padding: 16px 20px;
        }
        .dark .stat-cell {
            background: #1f2937;
        }
        .stat-val {
            font-size: 22px;
            font-weight: 500;
            color: #111827;
            line-height: 1;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .dark .stat-val { color: #f9fafb; }
        .stat-key {
            font-size: 11px;
            color: #6b7280;
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: .05em;
        }
        .stat-dot {
            display: inline-block;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .section-label {
            font-size: 11px;
            font-weight: 500;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .08em;
            margin: 0 0 12px;
        }
        .tbl-wrap {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
        }
        .dark .tbl-wrap { border-color: #374151; }
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .admin-table thead th {
            padding: 10px 16px;
            font-size: 11px;
            font-weight: 500;
            color: #6b7280;
            text-align: left;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            text-transform: uppercase;
            letter-spacing: .06em;
        }
        .dark .admin-table thead th {
            background: #111827;
            border-color: #374151;
            color: #9ca3af;
        }
        .admin-table tbody tr {
            border-bottom: 1px solid #f3f4f6;
        }
        .dark .admin-table tbody tr { border-color: #1f2937; }
        .admin-table tbody tr:last-child { border-bottom: none; }
        .admin-table tbody td {
            padding: 10px 16px;
            color: #111827;
            vertical-align: middle;
        }
        .dark .admin-table tbody td { color: #f3f4f6; }
        .admin-table tbody tr:hover td { background: #f9fafb; }
        .dark .admin-table tbody tr:hover td { background: #111827; }
        .cell-muted { color: #6b7280!important; }
        .cell-name  { font-weight: 500; }
        .badge {
            display: inline-block;
            font-size: 11px;
            font-weight: 500;
            padding: 2px 8px;
            border-radius: 4px;
        }
        .badge-pending  { background: #fffbeb; color: #92400e; }
        .badge-approved { background: #f0fdf4; color: #166534; }
        .badge-rejected { background: #fef2f2; color: #991b1b; }
        .badge-active   { background: #f0fdf4; color: #166534; }
        .badge-hidden   { background: #fef2f2; color: #991b1b; }
        .badge-review        { background: #eff6ff; color: #1e40af; }
        .badge-cerita-magang { background: #f5f3ff; color: #5b21b6; }
        .badge-menfess       { background: #fdf2f8; color: #9d174d; }
        .badge-qna           { background: #ecfeff; color: #155e75; }
        .badge-other         { background: #f9fafb; color: #374151; }

        .review-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px 20px;
            transition: border-color .15s;
        }
        .dark .review-card {
            background: #1f2937;
            border-color: #374151;
        }
        .review-card:hover { border-color: #d1d5db; }
        .dark .review-card:hover { border-color: #4b5563; }
        .badge-warning { background: #fffbeb; color: #92400e; }
        .btn-detail {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            font-weight: 500;
            color: #374151;
            background: transparent;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 4px 10px;
            cursor: pointer;
            transition: background .15s, border-color .15s;
        }
        .btn-detail:hover {
            background: #f9fafb;
            border-color: #d1d5db;
        }
        .dark .btn-detail {
            color: #d1d5db;
            border-color: #374151;
        }
        .dark .btn-detail:hover { background: #111827; }
    </style>

    {{-- STATISTIK KANTOR --}}
    <div style="margin-top:24px;">
        <p class="section-label">Statistik Kantor</p>
        <div class="stats-grid-4">
            @foreach([
                ['label' => 'Total',    'value' => $data['totalOffices'],    'color' => '#6366f1'],
                ['label' => 'Pending',  'value' => $data['pendingOffices'],  'color' => '#d97706'],
                ['label' => 'Approved', 'value' => $data['approvedOffices'], 'color' => '#16a34a'],
                ['label' => 'Rejected', 'value' => $data['rejectedOffices'], 'color' => '#dc2626'],
            ] as $stat)
            <div class="stat-cell">
                <div class="stat-val">
                    <span class="stat-dot" style="background:{{ $stat['color'] }};"></span>
                    {{ $stat['value'] }}
                </div>
                <div class="stat-key">{{ $stat['label'] }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- STATISTIK REVIEW --}}
    <div style="margin-top:24px;">
        <p class="section-label">Statistik Review</p>
        <div class="stats-grid-5">
            @foreach([
                ['label' => 'Total',         'value' => $data['totalReviews'], 'color' => '#6366f1'],
                ['label' => 'Review',        'value' => $data['reviewCount'],  'color' => '#2563eb'],
                ['label' => 'Cerita Magang', 'value' => $data['magangCount'],  'color' => '#7c3aed'],
                ['label' => 'Menfess',       'value' => $data['menfessCount'], 'color' => '#db2777'],
                ['label' => 'QnA',           'value' => $data['qnaCount'],      'color' => '#0891b2'],
            ] as $stat)
            <div class="stat-cell">
                <div class="stat-val">
                    <span class="stat-dot" style="background:{{ $stat['color'] }};"></span>
                    {{ $stat['value'] }}
                </div>
                <div class="stat-key">{{ $stat['label'] }}</div>
            </div>
            @endforeach
        </div>
        {{-- <div class="stats-grid-3">
            @foreach([
                ['label' => 'Disembunyikan', 'value' => $data['hiddenReviews'], 'color' => '#dc2626'],
                ['label' => 'Anonim',        'value' => $data['anonReviews'],   'color' => '#9ca3af'],
            ] as $stat)
            <div class="stat-cell">
                <div class="stat-val">
                    <span class="stat-dot" style="background:{{ $stat['color'] }};"></span>
                    {{ $stat['value'] }}
                </div>
                <div class="stat-key">{{ $stat['label'] }}</div>
            </div>
            @endforeach
        </div> --}}
    </div>

    {{-- TABEL KANTOR --}}
    <div style="margin-top:24px;">
        <p class="section-label">Daftar Kantor yang Diajukan</p>
        <div class="tbl-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nama Kantor</th>
                        <th>Status</th>
                        <th>Di-review oleh</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['offices'] as $office)
                    @php
                        $statusBadge = match($office->status) {
                            'approved' => 'badge-approved',
                            'rejected' => 'badge-rejected',
                            default    => 'badge-pending',
                        };
                        $statusLabel = match($office->status) {
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                            default    => 'Pending',
                        };
                    @endphp
                    <tr>
                        <td class="cell-name">{{ $office->name }}</td>
                        <td><span class="badge {{ $statusBadge }}">{{ $statusLabel }}</span></td>
                        <td class="cell-muted">{{ $office->reviewedBy?->name ?? '—' }}</td>
                        <td class="cell-muted">{{ $office->created_at->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="padding:24px;text-align:center;color:#9ca3af;font-size:13px;">
                            Belum ada kantor
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- DAFTAR REVIEW (Card List) --}}
    <div style="margin-top:24px; margin-bottom:32px;">
        <p class="section-label">Daftar Review</p>

        <div style="display:flex; flex-direction:column; gap:8px;">
            @forelse($data['reviews'] as $review)
            @php
                $typeBadge = match($review->type) {
                    'review'        => ['label' => 'Review',        'class' => 'badge-review'],
                    'cerita_magang' => ['label' => 'Cerita Magang', 'class' => 'badge-cerita-magang'],
                    'menfess'       => ['label' => 'Menfess',       'class' => 'badge-menfess'],
                    'qna'           => ['label' => 'Q&A',           'class' => 'badge-qna'],
                    default         => ['label' => ucfirst($review->type), 'class' => 'badge-other'],
                };
            @endphp

            <div class="review-card">
                {{-- HEADER --}}
                <div style="margin-bottom:6px;">
                    <span class="cell-name" style="font-size:14px;">
                        {{ $review->office?->name ?? '—' }}
                    </span>
                    <div style="font-size:11px; color:#6b7280; margin-top:2px;">
                        {{ $review->user?->name ?? 'Anonim' }} · {{ $review->created_at->format('d M Y H:i') }}
                    </div>
                </div>

                {{-- BODY --}}
                @if($review->experience)
                <div style="font-size:13px; color:#374151; margin-bottom:4px; line-height:1.6;">
                    <span style="color:#6b7280;">📝</span>
                    {{ Str::limit($review->experience, 120) }}
                </div>
                @endif

                @if($review->positive_notes)
                <div style="font-size:13px; color:#15803d; margin-bottom:4px; line-height:1.6;">
                    <span>✅</span>
                    {{ Str::limit($review->positive_notes, 120) }}
                </div>
                @endif

                {{-- FOOTER --}}
                <div style="display:flex; flex-wrap:wrap; align-items:center; gap:6px; margin-top:10px;">
                    <span class="badge {{ $typeBadge['class'] }}">Tipe: {{ $typeBadge['label'] }}</span>

                    @if($review->is_anonymous)
                        <span class="badge badge-warning">Identitas: Anonim</span>
                    @else
                        <span class="badge badge-approved">Identitas: Publik</span>
                    @endif

                    @if($review->is_hidden)
                        <span class="badge badge-rejected">Visibilitas: Disembunyikan</span>
                    @else
                        <span class="badge badge-approved">Visibilitas: Publik</span>
                    @endif

                    @if($review->deleted_at)
                        <span class="badge badge-rejected">Dihapus</span>
                    @else
                        <span class="badge badge-approved">Aktif</span>
                    @endif

                    {{-- Tombol detail --}}
                    <div style="margin-left:auto;">
                        <button
                            type="button"
                            class="btn-detail"
                            wire:click="mountAction('lihat_detail', { record: {{ $review->id }} })"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" style="width:13px;height:13px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.641 0-8.574-3.007-9.964-7.178z" /><circle cx="12" cy="12" r="3" /></svg>
                            Lihat detail
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div style="padding:24px;text-align:center;color:#9ca3af;font-size:13px;border:1px solid #e5e7eb;border-radius:12px;">
                Belum ada review
            </div>
            @endforelse
        </div>
    </div>
</x-filament-panels::page>