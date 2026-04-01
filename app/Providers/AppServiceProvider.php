<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Proveedor de servicios principal de la aplicación.
 *
 * Se utiliza para registrar servicios personalizados en el contenedor
 * de dependencias de Laravel y ejecutar código de inicialización
 * después de que todos los servicios hayan sido registrados.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Registra los servicios de la aplicación.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Ejecuta la inicialización de los servicios de la aplicación.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
