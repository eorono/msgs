<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

/**
 * Controlador para la gestión del perfil del usuario autenticado.
 *
 * Permite al usuario ver y editar su información personal,
 * actualizar su contraseña y eliminar su cuenta.
 *
 * Rutas:
 * - GET    /profile -> edit    (profile.edit)
 * - PATCH  /profile -> update  (profile.update)
 * - DELETE /profile -> destroy (profile.destroy)
 * Middleware: auth
 */
class ProfileController extends Controller
{
    /**
     * Muestra el formulario de edición del perfil del usuario.
     *
     * @param  Request $request Solicitud HTTP con el usuario autenticado
     * @return View Vista de edición del perfil con los datos del usuario
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Actualiza la información del perfil del usuario.
     *
     * Actualiza el nombre y email del usuario. Si el email ha cambiado,
     * se restablece la verificación del email (email_verified_at = null).
     *
     * @param  ProfileUpdateRequest $request Solicitud validada con los datos del perfil
     * @return RedirectResponse Redirige a la vista de edición con estado de éxito
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Elimina la cuenta del usuario autenticado.
     *
     * Requiere la contraseña actual del usuario para confirmar la eliminación.
     * Cierra la sesión, elimina el usuario de la base de datos e invalida
     * la sesión actual.
     *
     * @param  Request $request Solicitud HTTP con la contraseña de confirmación
     * @return RedirectResponse Redirige a la página principal
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
