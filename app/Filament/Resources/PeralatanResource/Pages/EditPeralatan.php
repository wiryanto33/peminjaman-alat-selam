<?php

namespace App\Filament\Resources\PeralatanResource\Pages;

use App\Filament\Resources\PeralatanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPeralatan extends EditRecord
{
    protected static string $resource = PeralatanResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
