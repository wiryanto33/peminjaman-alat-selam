<?php

namespace App\Filament\Resources\PengembalianAlatResource\Pages;

use App\Filament\Resources\PengembalianAlatResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPengembalianAlats extends ListRecords
{
    protected static string $resource = PengembalianAlatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
