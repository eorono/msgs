<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Controlador para la gestión de usuarios del sistema.
 *
 * Permite listar todos los usuarios registrados y crear nuevos usuarios
 * con soporte para identificadores de Telegram y números de WhatsApp.
 *
 * Rutas:
 * - GET  /users  -> index  (users.index)
 * - POST /users  -> store  (users.store)
 * Middleware: auth
 */
class UserController extends Controller
{
    /**
     * Muestra la lista de todos los usuarios registrados.
     *
     * Obtiene todos los usuarios de la base de datos y los pasa
     * a la vista 'users.index' para su visualización.
     *
     * @return \Illuminate\View\View Vista con la lista de usuarios
     */
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    /**
     * Crea un nuevo usuario en el sistema.
     *
     * Valida los datos del formulario (nombre, email, telegram_chat_id, whatsapp_number).
     * Limpia el número de WhatsApp eliminando caracteres no numéricos.
     * Genera una contraseña aleatoria de 12 caracteres para el nuevo usuario.
     *
     * @param  Request $request Solicitud HTTP con los datos del nuevo usuario
     * @return \Illuminate\Http\RedirectResponse Redirige con mensaje de éxito
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'telegram_chat_id' => ['nullable', 'string', 'max:255', 'unique:users'],
            'whatsapp_number' => ['nullable', 'string', 'max:255', 'unique:users'],
        ]);

        $cleanedWhatsapp = $request->whatsapp_number ? preg_replace('/[^0-9]/', '', $request->whatsapp_number) : null;

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'telegram_chat_id' => $request->telegram_chat_id,
            'whatsapp_number' => $cleanedWhatsapp,
            'password' => Hash::make(Str::random(12)), // Contraseña aleatoria por defecto
        ]);

        return back()->with('success', 'User successfully created!');
    }

    /**
     * Muestra el formulario para editar un usuario existente.
     *
     * @param  User $user Usuario a editar
     * @return \Illuminate\View\View Vista con el formulario de edición
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Actualiza la información de un usuario en el sistema.
     *
     * @param  Request $request Solicitud HTTP con los nuevos datos
     * @param  User    $user    Usuario a actualizar
     * @return \Illuminate\Http\RedirectResponse Redirige con mensaje de éxito
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'telegram_chat_id' => ['nullable', 'string', 'max:255', 'unique:users,telegram_chat_id,' . $user->id],
            'whatsapp_number' => ['nullable', 'string', 'max:255', 'unique:users,whatsapp_number,' . $user->id],
        ]);

        $cleanedWhatsapp = $request->whatsapp_number ? preg_replace('/[^0-9]/', '', $request->whatsapp_number) : null;

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'telegram_chat_id' => $request->telegram_chat_id,
            'whatsapp_number' => $cleanedWhatsapp,
        ]);

        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }

    /**
     * Elimina un usuario de la base de datos junto con su historial de mensajes.
     *
     * @param  User $user Usuario a eliminar
     * @return \Illuminate\Http\RedirectResponse Redirige con mensaje de éxito
     */
    public function destroy(User $user)
    {
        // Eliminamos el historial de mensajes asociados (enviados y recibidos)
        // para evitar errores de integridad referencial.
        $user->messages()->delete(); 
        \App\Models\Message::where('recipient_id', $user->id)->delete();
        
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully!');
    }
}
