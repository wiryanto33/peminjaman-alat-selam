<?php

namespace App\Filament\Resources\PeminjamanAlatResource\Pages;

use App\Filament\Resources\PeminjamanAlatResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPeminjamanAlat extends EditRecord
{
    protected static string $resource = PeminjamanAlatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Peminjaman Berhasil Diperbarui')
            ->body('Data peminjaman berhasil diperbarui.');
    }
}
