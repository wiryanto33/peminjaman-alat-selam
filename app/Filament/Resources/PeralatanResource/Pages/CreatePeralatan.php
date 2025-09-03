<?php

namespace App\Filament\Resources\PeralatanResource\Pages;

use App\Filament\Resources\PeralatanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePeralatan extends CreateRecord
{
    protected static string $resource = PeralatanResource::class;
    protected static bool $canCreateAnother = false;

    //customize redirect after create
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
