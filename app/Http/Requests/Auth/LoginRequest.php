<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Solicitud de validación para el inicio de sesión de usuarios.
 *
 * Implementa rate limiting (máximo 5 intentos) para prevenir ataques
 * de fuerza bruta. Valida las credenciales de email y contraseña.
 */
class LoginRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     *
     * @return bool Siempre retorna true ya que cualquier usuario puede intentar iniciar sesión
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtiene las reglas de validación para la solicitud.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Intenta autenticar al usuario con las credenciales proporcionadas.
     *
     * Primero verifica que no se haya excedido el límite de intentos.
     * Luego intenta autenticar con email y password. Si falla,
     * incrementa el contador de rate limiting y lanza una excepción de validación.
     *
     * @throws ValidationException Si las credenciales son inválidas
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Verifica que la solicitud no esté limitada por rate limiting.
     *
     * Si se han realizado más de 5 intentos fallidos, dispara un evento
     * de bloqueo y lanza una excepción con el tiempo restante.
     *
     * @throws ValidationException Si se ha excedido el límite de intentos
     */
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

    /**
     * Obtiene la clave de throttling para la solicitud.
     *
     * Combina el email (en minúsculas y transliterado) con la dirección IP
     * del cliente para crear una clave única para el rate limiting.
     *
     * @return string Clave de throttling
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
