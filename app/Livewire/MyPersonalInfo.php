<?php

namespace App\Livewire;

use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;
use Jeffgreco13\FilamentBreezy\Livewire\PersonalInfo;
use Livewire\Component;

class MyPersonalInfo extends PersonalInfo
{
    public array $only = [
        'name',
        'email',
        'pangkat',
        'nrp',
        'jabatan',
    ];

    protected function getPangkatComponent(): TextInput
    {
        return TextInput::make('pangkat')
            ->label(__('filament-breezy::default.fields.pangkat'));
    }

    protected function getNrpComponent(): TextInput
    {
        return TextInput::make('nrp')
            ->label(__('filament-breezy::default.fields.nrp'));
    }

    protected function getJabatanComponent(): TextInput
    {
        return TextInput::make('jabatan')
            ->label(__('filament-breezy::default.fields.jabatan'));
    }

    protected function getProfileFormSchema(): array
    {
        $groupFields = Group::make([
            $this->getNameComponent(),
            $this->getEmailComponent(),
            $this->getPangkatComponent(),
            $this->getNrpComponent(),
            $this->getJabatanComponent(),
        ])->columnSpan(2);

        return ($this->hasAvatars)
            ? [filament('filament-breezy')->getAvatarUploadComponent(), $groupFields]
            : [$groupFields];
    }
}
