<?php

namespace App\Filament\Widgets;

use App\Models\PeminjamanAlat;
use App\Models\PengembalianAlat;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class SimpleRecentActivityWidget extends Widget
{
    protected static string $view = 'filament.widgets.simple-recent-activity';

    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $user = Auth::user();
        $isAdmin = $user?->hasAnyRole(['super_admin', 'admin']) ?? false;

        // Query peminjaman
        $peminjamanQuery = PeminjamanAlat::with(['user', 'items.peralatan']);
        if (!$isAdmin) {
            $peminjamanQuery->where('user_id', Auth::id());
        }
        $peminjaman = $peminjamanQuery->latest()->limit(5)->get();

        // Query pengembalian
        $pengembalianQuery = PengembalianAlat::with(['user', 'peminjamanItem.peralatan']);
        if (!$isAdmin) {
            $pengembalianQuery->where('user_id', Auth::id());
        }
        $pengembalian = $pengembalianQuery->latest()->limit(5)->get();

        return [
            'peminjaman' => $peminjaman,
            'pengembalian' => $pengembalian,
            'isAdmin' => $isAdmin,
        ];
    }
}
