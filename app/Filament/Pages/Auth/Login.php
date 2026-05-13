<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use MarcoGermani87\FilamentCaptcha\Forms\Components\CaptchaField;

class Login extends BaseLogin
{
    
    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data');
    }

    protected  function getCredentialsFromFormData(array $data): array
    {
        return [
            'email' => $data['email'],
            'password' => $data['password'],
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            // 'data.login' => __('filament-panels::pages/auth/login.messages.failed'),
            'data.login' => 'Login failed. Please check your email or Employee ID (NIP) and password.',
        ]);
    }

    public function authenticate(): ?LoginResponse
    {
        $response = parent::authenticate();

        $user = Auth::user();

        if (! $user || ! $user->is_admin) {
            Auth::logout();

            throw ValidationException::withMessages([
                'data.email' => "Akses diblokir",
            ]);
        }

        return $response;
    }
}
