<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\SendsMessages;
use Illuminate\Http\Request;

/**
 * Controlador invocable para el envío de mensajes masivos.
 *
 * Procesa las solicitudes de envío de mensajes desde el dashboard.
 * Valida los datos de entrada (plataforma, mensaje, usuarios),
 * resuelve el servicio de mensajería correspondiente desde la
 * configuración de plataformas y ejecuta el envío masivo.
 *
 * Ruta: POST /send
 * Nombre: send
 * Middleware: auth
 */
class SendMessage extends Controller
{
    /**
     * Procesa la solicitud de envío de mensajes masivos.
     *
     * Valida que:
     * - La plataforma sea una de las disponibles en config('platforms')
     * - El mensaje no esté vacío
     * - Se seleccione al menos un usuario válido
     *
     * Resuelve el servicio de la plataforma desde el contenedor de servicios
     * y ejecuta sendMassMessage() con los usuarios y el mensaje proporcionados.
     *
     * @param  Request $request Solicitud HTTP con platform, message y users
     * @return \Illuminate\Http\RedirectResponse Redirige al dashboard con mensaje de éxito
     */
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'platform' => 'required|in:' . implode(',', array_keys(config('platforms'))),
            'message' => 'required',
            'users' => 'required|array',
            'users.*' => 'int|exists:users,id'
        ]);

        $platformService = app(config('platforms')[$data['platform']]);
        $users = User::findMany($data['users'])->all();
        $platformService->sendMassMessage($users, $data['message']);

        return back()->with('success', 'Message sent');
    }
}
