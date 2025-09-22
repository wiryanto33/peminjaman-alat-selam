<?php

namespace App\Filament\Widgets;

use App\Models\PeminjamanAlat;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PeminjamanChart extends ChartWidget
{
    protected static ?string $heading = 'Statistik Peminjaman';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $user = Auth::user();
        $isAdmin = $user?->hasAnyRole(['super_admin', 'admin']) ?? false;

        // Data untuk 12 bulan terakhir
        $months = [];
        $data = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');

            $query = PeminjamanAlat::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year);

            // Jika bukan admin, filter hanya data user sendiri
            if (!$isAdmin) {
                $query->where('user_id', Auth::id());
            }

            $data[] = $query->count();
        }

        return [
            'datasets' => [
                [
                    'label' => $isAdmin ? 'Total Peminjaman' : 'Peminjaman Saya',
                    'data' => $data,
                    'backgroundColor' => '#10B981',
                    'borderColor' => '#059669',
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
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
        ];
    }
}
