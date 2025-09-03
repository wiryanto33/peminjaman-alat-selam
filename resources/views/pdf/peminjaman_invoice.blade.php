@php
    $user = $record->user;
    $items = $record->items;
    $nomor = 'INV-PJ-' . str_pad((string)$record->id, 6, '0', STR_PAD_LEFT);
@endphp
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>{{ $nomor }}</title>
<style>
    * { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; }
    body { font-size: 12px; color: #111; }
    .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; }
    .brand h2 { margin: 0 0 2px 0; }
    .muted { color: #666; }
    .box { border:1px solid #ddd; padding:10px; border-radius:6px; }
    table { width:100%; border-collapse: collapse; }
    th, td { border:1px solid #ddd; padding:8px; }
    th { background:#f3f4f6; text-align:left; }
    .right { text-align:right; }
    .mt-8 { margin-top: 16px; }
    .mt-12 { margin-top: 24px; }
    .badge { display:inline-block; padding:2px 8px; border-radius:10px; font-size:11px; }
    .badge.approved { background:#dcfce7; color:#166534; }
    .badge.pending  { background:#fef9c3; color:#854d0e; }
    .badge.rejected { background:#fee2e2; color:#991b1b; }
</style>
</head>
<body>

<div class="header">
    <div class="brand">
        <h2>INVOICE PEMINJAMAN ALAT</h2>
        <div class="muted">Nomor: {{ $nomor }}</div>
    </div>
    <div class="box">
        <div><strong>Tanggal Pinjam:</strong> {{ \Illuminate\Support\Carbon::parse($record->tanggal_pinjam)->isoFormat('D MMMM Y') }}</div>
        <div><strong>Status:</strong>
            <span class="badge {{ $record->approval }}">{{ strtoupper($record->approval) }}</span>
        </div>
    </div>
</div>

<div class="box">
    <strong>Data Peminjam</strong>
    <div>{{ trim(($user->pangkat ?? '').' '.($user->name ?? '-')) }}</div>
    @if(!empty($user->email))<div class="muted">{{ $user->email }}</div>@endif
</div>

<div class="mt-12">
    <table>
        <thead>
        <tr>
            <th style="width:30px;">No</th>
            <th>Nama Alat</th>
            <th class="right" style="width:90px;">Jumlah</th>
        </tr>
        </thead>
        <tbody>
        @foreach($items as $i => $item)
            <tr>
                <td class="right">{{ $i+1 }}</td>
                <td>{{ $item->peralatan?->name ?? 'Alat' }}</td>
                <td class="right">{{ (int)$item->jumlah }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <th colspan="2" class="right">Total Qty</th>
            <th class="right">{{ $items->sum('jumlah') }}</th>
        </tr>
        </tfoot>
    </table>
</div>

@if(!empty($record->keterangan))
    <div class="mt-8">
        <strong>Keterangan:</strong>
        <div class="muted">{{ $record->keterangan }}</div>
    </div>
@endif

<div class="mt-12 muted">
    Dicetak pada: {{ now()->isoFormat('D MMMM Y, HH:mm') }}
</div>

</body>
</html>
