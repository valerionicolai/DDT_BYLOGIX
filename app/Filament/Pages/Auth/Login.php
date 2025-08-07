<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getEmailFormComponent()
                    ->label('Email Address')
                    ->placeholder('Enter your email address'),
                $this->getPasswordFormComponent()
                    ->label('Password')
                    ->placeholder('Enter your password'),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data');
    }

    protected function getEmailFormComponent(): TextInput
    {
        return TextInput::make('email')
            ->label(__('filament-panels::pages/auth/login.form.email.label'))
            ->email()
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getPasswordFormComponent(): TextInput
    {
        return TextInput::make('password')
            ->label(__('filament-panels::pages/auth/login.form.password.label'))
            ->password()
            ->required()
            ->extraInputAttributes(['tabindex' => 2]);
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.email' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }

    public function getTitle(): string
    {
        return 'DDT by Logix - Admin Login';
    }

    public function getHeading(): string
    {
        return 'Welcome to DDT by Logix';
    }

    public function getSubheading(): ?string
    {
        return 'Please sign in to access the admin panel';
    }

    protected function getRedirectUrl(): ?string
    {
        return '/admin';
    }
}
