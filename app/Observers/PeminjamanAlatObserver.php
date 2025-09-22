<?php

namespace App\Observers;

use App\Filament\Resources\PeminjamanAlatResource;
use App\Models\PeminjamanAlat;
use App\Models\Peralatan;
use App\Models\User;
// ⬇️ pastikan IMPORT yang benar:
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action as NotificationAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PeminjamanAlatObserver
{
    public function created(PeminjamanAlat $loan): void
    {
        // Beri tahu admin ada pengajuan baru
        $loan->loadMissing(['items.peralatan', 'user']);
        $summary = $loan->items->map(fn($it) => ($it->peralatan?->name ?? 'Alat') . ' x ' . $it->jumlah)->join(', ');
        $admins  = User::role(['admin', 'super_admin'])->get();

        DB::afterCommit(function () use ($admins, $loan, $summary) {
            Notification::make()
                ->title('Permintaan Peminjaman Baru')
                ->body("{$loan->user?->name} mengajukan peminjaman: {$summary}.")
                ->icon('heroicon-o-rectangle-stack')
                ->actions([
                NotificationAction::make('Lihat')
                        ->url(PeminjamanAlatResource::getUrl('edit', ['record' => $loan]))
                        ->button(),
                ])
                ->sendToDatabase($admins);
        });
    }

    public function updating(PeminjamanAlat $loan): void
    {
        if (! $loan->isDirty('approval')) return;

        $from = $loan->getOriginal('approval');
        $to   = $loan->approval;

        // ====== stok handling (punyamu sebelumnya) ======
        if (in_array($from, ['pending', 'rejected'], true) && $to === 'approved') {
            $this->reduceStock($loan);
        }
        if ($from === 'approved' && in_array($to, ['pending', 'rejected'], true)) {
            $this->restoreStock($loan);
        }
        // ================================================

        // Beri tahu user kalau status berubah
        $loan->loadMissing(['items.peralatan', 'user']);
        $summary = $loan->items->map(fn($it) => ($it->peralatan?->name ?? 'Alat') . ' x ' . $it->jumlah)->join(', ');
        $title   = match ($to) {
            'approved' => 'Peminjaman Disetujui',
            'rejected' => 'Peminjaman Ditolak',
            default    => 'Status Peminjaman Dipending',
        };
        $body    = "Status peminjaman Anda: {$to}. Rincian: {$summary}.";

        DB::afterCommit(function () use ($loan, $title, $body) {
            Notification::make()
                ->title($title)
                ->body($body)
                ->icon('heroicon-o-information-circle')
                ->actions([
                NotificationAction::make('Lihat')
                        ->url(PeminjamanAlatResource::getUrl('edit', ['record' => $loan]))
                        ->button(),
                ])
                ->sendToDatabase($loan->user); // kirim ke user peminjam
        });
    }

    public function deleting(PeminjamanAlat $loan): void
    {
        if ($loan->approval === 'approved') {
            $this->restoreStock($loan);
        }
    }

    private function reduceStock(PeminjamanAlat $loan): void
    {
        DB::transaction(function () use ($loan) {
            $loan->loadMissing('items.peralatan');

            // Validasi stok dulu
            foreach ($loan->items as $it) {
                $p = Peralatan::whereKey($it->peralatan_id)->lockForUpdate()->first();
                if (! $p) {
                    throw ValidationException::withMessages([
                        'items' => "Peralatan dengan ID {$it->peralatan_id} tidak ditemukan.",
                    ]);
                }
                if ($p->stock < $it->jumlah) {
                    throw ValidationException::withMessages([
                        'approval' => "Stok {$p->name} tidak mencukupi. Tersedia: {$p->stock}, diminta: {$it->jumlah}",
                    ]);
                }
            }

            // Kurangi
            foreach ($loan->items as $it) {
                Peralatan::whereKey($it->peralatan_id)
                    ->lockForUpdate()
                    ->decrement('stock', $it->jumlah);
            }
        });
    }

    private function restoreStock(PeminjamanAlat $loan): void
    {
        DB::transaction(function () use ($loan) {
            $loan->loadMissing('items.peralatan');

            foreach ($loan->items as $it) {
                Peralatan::whereKey($it->peralatan_id)
                    ->lockForUpdate()
                    ->increment('stock', $it->jumlah);
            }
        });
    }
}
