<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        if (! $this->migrator->exists('KaidoSetting.auth_logo_path')) {
            $this->migrator->add('KaidoSetting.auth_logo_path', null);
        }

        if (! $this->migrator->exists('KaidoSetting.auth_background_path')) {
            $this->migrator->add('KaidoSetting.auth_background_path', null);
        }

        if (! $this->migrator->exists('KaidoSetting.auth_card_opacity')) {
            $this->migrator->add('KaidoSetting.auth_card_opacity', 90);
        }
    }
};
