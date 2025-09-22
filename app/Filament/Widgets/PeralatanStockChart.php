<?php

namespace App\Filament\Widgets;

use App\Models\Peralatan;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class PeralatanStockChart extends ChartWidget
{
    protected static ?string $heading = 'Stock Peralatan';

    protected static ?int $sort = 4;

    // Widget ini hanya ditampilkan untuk admin
    public static function canView(): bool
    {
        return Auth::user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    protected function getData(): array
    {
        // Ambil 10 peralatan dengan stock terbanyak
        $peralatan = Peralatan::orderBy('stock', 'desc')
            ->limit(10)
            ->get();

        $labels = $peralatan->pluck('name')->toArray();
        $data = $peralatan->pluck('stock')->toArray();

        // Generate random colors untuk setiap bar
        $colors = [];
        for ($i = 0; $i < count($data); $i++) {
            $colors[] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
        }

        return [
            'datasets' => [
                [
                    'label' => 'Stock',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => $colors,
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            return context.label + ": " + context.parsed + " unit";
                        }'
                    ]
                ]
            ],
        ];
    }
}
