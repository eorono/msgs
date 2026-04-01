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
}
