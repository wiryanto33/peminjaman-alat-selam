<?php

namespace App\Filament\Resources\PeminjamanAlatResource\Pages;

use App\Filament\Resources\PeminjamanAlatResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePeminjamanAlat extends CreateRecord
{
    protected static string $resource = PeminjamanAlatResource::class;

    /** Flag: apakah user/admin memilih Approved di form? */
    protected bool $approveAfterSave = false;

    /**
     * Tangkap niat approval SEBELUM create, tapi paksa disimpan sebagai pending dulu.
     * Repeater relasi `items` akan disimpan otomatis oleh Filament.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Simpan niat awal approval
        $this->approveAfterSave = (($data['approval'] ?? 'pending') === 'approved');

        // Pastikan user biasa tidak bisa set user_id / approval
        if (! auth()->user()?->hasAnyRole(['admin', 'super_admin'])) {
            $data['user_id']  = auth()->id();
            $data['approval'] = 'pending';
            return $data;
        }

        // Admin: tetap paksa pending dulu agar stok dipotong SETELAH items tersimpan
        $data['approval'] = 'pending';
        return $data;
    }

    /**
     * Biarkan Filament membuat record utama.
     * JANGAN sisipkan items manual di sini — Filament akan menyimpan relasi repeater otomatis.
     */
    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        return static::getModel()::create($data);
    }

    /**
     * Setelah record + relationships (items) tersimpan oleh Filament,
     * bila tadi admin memilih Approved, barulah flip approval → Observer akan kurangi stok.
     */
    protected function afterCreate(): void
    {
        if ($this->approveAfterSave) {
            $this->record->update(['approval' => 'approved']);
        }
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Peminjaman Berhasil Dibuat')
            ->body('Data peminjaman berhasil disimpan.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
