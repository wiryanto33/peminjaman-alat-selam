<?php

namespace App\Filament\Widgets;

use App\Models\PeminjamanAlat;
use App\Models\PengembalianAlat;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class ApprovalStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Status Approval';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $user = Auth::user();
        $isAdmin = $user?->hasAnyRole(['super_admin', 'admin']) ?? false;

        // Query peminjaman
        $peminjamanQuery = PeminjamanAlat::query();
        $pengembalianQuery = PengembalianAlat::query();

        // Jika bukan admin, filter hanya data user sendiri
        if (!$isAdmin) {
            $peminjamanQuery->where('user_id', Auth::id());
            $pengembalianQuery->where('user_id', Auth::id());
        }

        // Data peminjaman
        $peminjamanPending = $peminjamanQuery->where('approval', 'pending')->count();
        $peminjamanApproved = $peminjamanQuery->where('approval', 'approved')->count();
        $peminjamanRejected = $peminjamanQuery->where('approval', 'rejected')->count();

        // Data pengembalian
        $pengembalianPending = $pengembalianQuery->where('approval', 'pending')->count();
        $pengembalianApproved = $pengembalianQuery->where('approval', 'approved')->count();
        $pengembalianRejected = $pengembalianQuery->where('approval', 'rejected')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Peminjaman',
                    'data' => [$peminjamanPending, $peminjamanApproved, $peminjamanRejected],
                    'backgroundColor' => ['#F59E0B', '#10B981', '#EF4444'],
                ],
                [
                    'label' => 'Pengembalian',
                    'data' => [$pengembalianPending, $pengembalianApproved, $pengembalianRejected],
                    'backgroundColor' => ['#F97316', '#059669', '#DC2626'],
                ],
            ],
            'labels' => ['Pending', 'Approved', 'Rejected'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
            ],
        ];
    }
}
