<?php

namespace App\Filament\Resources\PengembalianAlatResource\Pages;

use App\Filament\Resources\PengembalianAlatResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPengembalianAlat extends EditRecord
{
    protected static string $resource = PengembalianAlatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn() => auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Pengembalian Diperbarui')
            ->body('Data pengembalian berhasil diperbarui.');
    }
}
