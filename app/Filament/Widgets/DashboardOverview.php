<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Peralatan;
use App\Models\PeminjamanAlat;
use App\Models\PengembalianAlat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class DashboardOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $isAdmin = $user?->hasAnyRole(['super_admin', 'admin']) ?? false;

        if ($isAdmin) {
            return $this->getAdminStats();
        }

        return $this->getUserStats();
    }

    protected function getAdminStats(): array
    {
        // Statistik untuk Admin
        $totalUsers = User::count();
        $totalPeralatan = Peralatan::count();
        $totalStock = Peralatan::sum('stock');

        // Peminjaman Statistics
        $totalPeminjaman = PeminjamanAlat::count();
        $peminjamanPending = PeminjamanAlat::where('approval', 'pending')->count();
        $peminjamanApproved = PeminjamanAlat::where('approval', 'approved')->count();
        $peminjamanRejected = PeminjamanAlat::where('approval', 'rejected')->count();

        // Pengembalian Statistics
        $totalPengembalian = PengembalianAlat::count();
        $pengembalianPending = PengembalianAlat::where('approval', 'pending')->count();
        $pengembalianApproved = PengembalianAlat::where('approval', 'approved')->count();

        // Peminjaman bulan ini
        $peminjamanBulanIni = PeminjamanAlat::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Pengembalian bulan ini
        $pengembalianBulanIni = PengembalianAlat::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return [
            Stat::make('Total Users', $totalUsers)
                ->description('Total pengguna terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Total Peralatan', $totalPeralatan)
                ->description('Jenis peralatan tersedia')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('info'),

            Stat::make('Total Stock', $totalStock)
                ->description('Total unit peralatan')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),

            Stat::make('Peminjaman Pending', $peminjamanPending)
                ->description('Menunggu persetujuan')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Pengembalian Pending', $pengembalianPending)
                ->description('Menunggu konfirmasi')
                ->descriptionIcon('heroicon-m-arrow-uturn-left')
                ->color('warning'),

            Stat::make('Peminjaman Bulan Ini', $peminjamanBulanIni)
                ->description('Total peminjaman ' . now()->format('M Y'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success'),
        ];
    }

    protected function getUserStats(): array
    {
        $userId = Auth::id();

        // Statistik untuk User biasa
        $myPeminjaman = PeminjamanAlat::where('user_id', $userId)->count();
        $myPeminjamanPending = PeminjamanAlat::where('user_id', $userId)
            ->where('approval', 'pending')->count();
        $myPeminjamanApproved = PeminjamanAlat::where('user_id', $userId)
            ->where('approval', 'approved')->count();

        $myPengembalian = PengembalianAlat::where('user_id', $userId)->count();
        $myPengembalianPending = PengembalianAlat::where('user_id', $userId)
            ->where('approval', 'pending')->count();

        // Total peralatan yang tersedia
        $totalPeralatanTersedia = Peralatan::where('stock', '>', 0)->count();

        return [
            Stat::make('My Peminjaman', $myPeminjaman)
                ->description('Total peminjaman saya')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('primary'),

            Stat::make('Peminjaman Pending', $myPeminjamanPending)
                ->description('Menunggu persetujuan')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Peminjaman Approved', $myPeminjamanApproved)
                ->description('Sudah disetujui')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('My Pengembalian', $myPengembalian)
                ->description('Total pengembalian saya')
                ->descriptionIcon('heroicon-m-arrow-uturn-left')
                ->color('info'),

            Stat::make('Pengembalian Pending', $myPengembalianPending)
                ->description('Menunggu konfirmasi')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Peralatan Tersedia', $totalPeralatanTersedia)
                ->description('Dapat dipinjam')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('success'),
        ];
    }
}
