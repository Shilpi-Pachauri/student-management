<?php

namespace App\Filament\Pages\Auth;
use Filament\Pages\Auth\Login as FilamentLogin;
use Filament\Forms\Components\Component;

class Login extends FilamentLogin{
    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'username' => $data['username'],
            'password' => $data['password'],
        ];
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('username')
            ->label('UserName')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }
}