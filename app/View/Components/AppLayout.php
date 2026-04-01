<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Componente de layout principal para páginas autenticadas.
 *
 * Renderiza la vista 'layouts.app' que incluye la barra de navegación,
 * el encabezado opcional y el slot para el contenido de la página.
 * Utiliza Tailwind CSS y Vite para los estilos y scripts.
 */
class AppLayout extends Component
{
    /**
     * Obtiene la vista que representa el componente.
     *
     * @return View Vista del layout principal
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
