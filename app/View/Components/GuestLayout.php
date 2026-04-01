<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Componente de layout para páginas de invitados (no autenticados).
 *
 * Renderiza la vista 'layouts.guest' que presenta un diseño centrado
 * con el logo de la aplicación y el contenido del formulario de autenticación.
 * Utilizado por las páginas de login, registro y recuperación de contraseña.
 */
class GuestLayout extends Component
{
    /**
     * Obtiene la vista que representa el componente.
     *
     * @return View Vista del layout de invitados
     */
    public function render(): View
    {
        return view('layouts.guest');
    }
}
