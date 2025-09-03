<?php

namespace App\Observers;

use App\Filament\Resources\PengembalianAlatResource;
use App\Models\PengembalianAlat;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action as NotificationAction;
use Illuminate\Support\Facades\DB;

class PengembalianAlatObserver
{
    public function created(PengembalianAlat $ret): void
    {
        // Notif ke admin: ada pengembalian baru
        $ret->loadMissing(['peminjamanItem.peralatan', 'user']);
        $alat  = $ret->peminjamanItem?->peralatan?->name ?? 'Alat';
        $qty   = (int) $ret->jumlah_dikembalikan;
        $admins = User::role(['admin', 'super_admin'])->get();

        DB::afterCommit(function () use ($admins, $ret, $alat, $qty) {
            Notification::make()
                ->title('Pengembalian Baru')
                ->body("{$ret->user?->name} mengembalikan {$alat} x {$qty}.")
                ->icon('heroicon-o-arrow-uturn-left')
                ->actions([
                    Action::make('Review')
                        ->url(PengembalianAlatResource::getUrl('edit', ['record' => $ret]))
                        ->button(),
                ])
                ->sendToDatabase($admins);
        });
    }

    public function updating(PengembalianAlat $ret): void
    {
        if (! $ret->isDirty('approval')) return;

        $from = $ret->getOriginal('approval');
        $to   = $ret->approval;

        // ===== stok balik / koreksi (punyamu) =====
        if (in_array($from, ['pending', 'rejected'], true) && $to === 'approved') {
            $this->restoreStock($ret);
        }
        if ($from === 'approved' && in_array($to, ['pending', 'rejected'], true)) {
            $this->reduceBackStock($ret);
        }
        // =========================================

        // Notif ke user
        $ret->loadMissing(['peminjamanItem.peralatan', 'user']);
        $alat = $ret->peminjamanItem?->peralatan?->name ?? 'Alat';
        $qty  = (int) $ret->jumlah_dikembalikan;
        $title = match ($to) {
            'approved' => 'Pengembalian Disetujui',
            'rejected' => 'Pengembalian Ditolak',
            default    => 'Pengembalian Dipending',
        };
        $body  = "Status pengembalian {$alat} x {$qty}: {$to}.";

        DB::afterCommit(function () use ($ret, $title, $body) {
            Notification::make()
                ->title($title)
                ->body($body)
                ->icon('heroicon-o-information-circle')
                ->actions([
                NotificationAction::make('Lihat')
                        ->url(PengembalianAlatResource::getUrl('edit', ['record' => $ret]))
                        ->button(),
                ])
                ->sendToDatabase($ret->user);
        });
    }

    public function deleting(PengembalianAlat $ret): void
    {
        if ($ret->approval === 'approved') {
            $this->reduceBackStock($ret);
        }
    }

    private function restoreStock(PengembalianAlat $ret): void
    {
        DB::transaction(function () use ($ret) {
            $item = $ret->peminjamanItem()->with('peralatan')->lockForUpdate()->first();
            if ($item?->peralatan) {
                $item->peralatan->increment('stock', (int) $ret->jumlah_dikembalikan);
            }
        });
    }

    private function reduceBackStock(PengembalianAlat $ret): void
    {
        DB::transaction(function () use ($ret) {
            $item = $ret->peminjamanItem()->with('peralatan')->lockForUpdate()->first();
            if ($item?->peralatan) {
                // jaga-jaga agar tidak minus
                $amount = min((int) $ret->jumlah_dikembalikan, (int) $item->peralatan->stock);
                $item->peralatan->decrement('stock', $amount);
            }
        });
    }
}
