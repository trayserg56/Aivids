<?php

namespace App\Filament\Concerns;

use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

trait HasSectionSettingsAction
{
    /**
     * @param  array<string, string>  $fields
     * @param  array<string, mixed>  $defaults
     */
    protected function makeSectionSettingsAction(string $key, array $fields, array $defaults): Action
    {
        $components = [];

        foreach ($fields as $field => $label) {
            $components[] = in_array($field, ['subtitle', 'footer'], true)
                ? Textarea::make($field)->label($label)->rows(2)->columnSpanFull()
                : TextInput::make($field)->label($label)->required()->maxLength(255);
        }

        return Action::make('sectionSettings')
            ->label('Настройки секции')
            ->icon('heroicon-o-cog-6-tooth')
            ->form($components)
            ->fillForm(fn (): array => SiteSetting::get($key, $defaults))
            ->action(function (array $data) use ($key): void {
                SiteSetting::set($key, $data);

                Notification::make()
                    ->title('Настройки секции сохранены')
                    ->success()
                    ->send();
            });
    }
}
