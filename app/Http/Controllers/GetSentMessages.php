<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Controlador invocable para obtener los mensajes enviados por el usuario.
 *
 * Recupera todos los mensajes asociados al usuario autenticado
 * y los pasa a la vista 'sent' para su visualización.
 *
 * Ruta: GET /sent
 * Nombre: sent
 * Middleware: auth
 */
class GetSentMessages extends Controller
{
    /**
     * Obtiene y muestra los mensajes enviados por el usuario autenticado.
     *
     * Recupera la colección de mensajes del usuario a través de la
     * relación 'messages' definida en el modelo User.
     *
     * @param  Request $request Solicitud HTTP
     * @return \Illuminate\View\View Vista 'sent' con los mensajes del usuario
     */
    public function __invoke(Request $request)
    {
        $messages = auth()->user()->messages;

        return view('sent', compact('messages'));
    }
}
