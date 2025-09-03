<?php

namespace App\Filament\Resources\PengembalianAlatResource\Pages;

use App\Filament\Resources\PengembalianAlatResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePengembalianAlat extends CreateRecord
{
    protected static string $resource = PengembalianAlatResource::class;

    /** Simpan niat awal jika admin memilih approved di form */
    protected bool $approveAfterSave = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->approveAfterSave = (($data['approval'] ?? 'pending') === 'approved');

        // User biasa tidak boleh set approval/user_id manual
        if (! auth()->user()?->hasAnyRole(['super_admin', 'admin'])) {
            $data['user_id']  = auth()->id();
            $data['approval'] = 'pending';
        } else {
            // Admin: tetap paksa pending dulu agar observer jalan setelah record tersimpan
            $data['approval'] = 'pending';
        }

        // Pastikan peminjaman_id ikut terisi dari item (jika belum)
        if (empty($data['peminjaman_id']) && !empty($data['peminjaman_item_id'])) {
            $item = \App\Models\PeminjamanItem::with('peminjaman')->find($data['peminjaman_item_id']);
            if ($item && $item->peminjaman) {
                $data['peminjaman_id'] = $item->peminjaman->id;
            }
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->approveAfterSave) {
            // Flip ke approved setelah create → Observer Pengembalian akan menambah stok
            $this->record->update(['approval' => 'approved']);
        }
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Pengembalian Tercatat')
            ->body('Data pengembalian berhasil disimpan.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
