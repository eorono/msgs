<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Solicitud de validación para la actualización del perfil de usuario.
 *
 * Valida que el nombre sea requerido (máximo 255 caracteres) y que el email
 * sea válido, único en la tabla de usuarios (ignorando el usuario actual).
 */
class ProfileUpdateRequest extends FormRequest
{
    /**
     * Obtiene las reglas de validación para la solicitud.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];
    }
}
