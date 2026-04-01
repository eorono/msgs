
<?php

/**
 * Configuración de plataformas de mensajería disponibles.
 *
 * Mapea cada nombre de plataforma a su clase de servicio correspondiente.
 * El controlador SendMessage utiliza esta configuración para resolver
 * el servicio correcto desde el contenedor de dependencias de Laravel.
 *
 * Plataformas disponibles:
 * - discord: Servicio simulado que registra en logs y BD
 * - slack: Servicio simulado que registra en logs y BD
 * - telegram: Servicio real que usa la API de Telegram
 * - whatsapp: Servicio real que usa la Evolution API
 *
 * Para agregar una nueva plataforma:
 * 1. Crear una clase que implemente App\Services\SendsMessages
 * 2. Agregar la entrada aquí: 'nombre' => \App\Services\NombreServicio::class
 */

return [
    'discord' => \App\Services\DiscordService::class,
    'slack' => \App\Services\SlackService::class,
    'telegram' => \App\Services\TelegramService::class,
    'whatsapp' => \App\Services\WhatsappService::class,
];
