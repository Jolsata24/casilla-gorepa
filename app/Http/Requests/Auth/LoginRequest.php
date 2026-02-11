<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // 1. Intentamos loguear y verificamos que el status sea 1 (Activo)
        // Esto evita que usuarios pendientes entren.
        $credentials = $this->only('email', 'password');
        $credentials['status'] = 1; 

        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            
            // 2. Si falla, revisamos si es porque está pendiente (Status 0)
            // para dar un mensaje claro al usuario.
            $user = User::where('email', $this->input('email'))->first();

            // Si la contraseña es correcta pero el status no es 1:
            if ($user && Hash::check($this->input('password'), $user->password)) {
                if ($user->status === 0) {
                    throw ValidationException::withMessages([
                        'email' => 'Su cuenta está registrada pero pendiente de aprobación por el administrador.',
                    ]);
                }
            }

            // 3. Error genérico (credenciales mal)
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}