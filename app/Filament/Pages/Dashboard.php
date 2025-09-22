<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DashboardOverview;
use App\Filament\Widgets\PeminjamanChart;
use App\Filament\Widgets\ApprovalStatusChart;
use App\Filament\Widgets\PeralatanStockChart;
use App\Filament\Widgets\SimpleRecentActivityWidget;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        $user = Auth::user();
        $isAdmin = $user?->hasAnyRole(['super_admin', 'admin']) ?? false;

        $widgets = [
            DashboardOverview::class,
            PeminjamanChart::class,
            ApprovalStatusChart::class,
        ];

        // Widget khusus admin
        if ($isAdmin) {
            $widgets[] = PeralatanStockChart::class;
        }

        $widgets[] = SimpleRecentActivityWidget::class;

        return $widgets;
    }

    public function getColumns(): int | string | array
    {
        return 2; // Layout 2 kolom
    }

    public function getTitle(): string
    {
        $user = Auth::user();
        $isAdmin = $user?->hasAnyRole(['super_admin', 'admin']) ?? false;

        if ($isAdmin) {
            return 'Dashboard Admin';
        }

        return 'Dashboard - Selamat Datang, ' . ($user->name ?? 'User');
    }
}
