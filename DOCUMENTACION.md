# Documentación del Proyecto - msgs

## Descripción General

**msgs** es una aplicación de mensajería masiva multiplataforma construida con **Laravel 11.9**. Permite a los usuarios autenticados enviar mensajes a múltiples destinatarios a través de cuatro plataformas de mensajería: **Discord**, **Slack**, **Telegram** y **WhatsApp**.

Los mensajes se registran en la base de datos y en los logs de Laravel, y las plataformas de Telegram y WhatsApp realizan envíos reales a través de sus respectivas APIs.

---

## Tecnologías Utilizadas

| Tecnología | Versión | Uso |
|---|---|---|
| PHP | 8.2+ | Lenguaje de programación principal |
| Laravel | 11.9 | Framework backend |
| Laravel Breeze | - | Autenticación con Blade |
| Laravel Sail | - | Docker para desarrollo |
| Pest PHP | - | Framework de testing |
| Vite | - | Compilación de assets frontend |
| Tailwind CSS | - | Framework CSS |
| MySQL | 8.0 | Base de datos |
| Redis | - | Sistema de caché |
| Alpine.js | - | Interactividad en vistas Blade |

---

## Estructura del Proyecto

```
msgs/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # Controladores de la aplicación
│   │   │   ├── Auth/             # Controladores de autenticación (Breeze)
│   │   │   ├── SendMessage.php   # Envío de mensajes masivos
│   │   │   ├── GetSentMessages.php # Historial de mensajes enviados
│   │   │   ├── UserController.php  # Gestión de usuarios
│   │   │   └── ProfileController.php # Gestión del perfil
│   │   └── Requests/             # Solicitudes de validación
│   │       └── Auth/             # Solicitudes de autenticación
│   ├── Models/                   # Modelos Eloquent
│   │   ├── User.php              # Modelo de usuario
│   │   └── Message.php           # Modelo de mensaje
│   ├── Services/                 # Servicios de mensajería
│   │   ├── SendsMessages.php     # Interfaz del contrato de mensajería
│   │   ├── DiscordService.php    # Servicio de Discord (simulado)
│   │   ├── SlackService.php      # Servicio de Slack (simulado)
│   │   ├── TelegramService.php   # Servicio de Telegram (API real)
│   │   └── WhatsappService.php   # Servicio de WhatsApp (Evolution API)
│   ├── Providers/                # Service Providers
│   └── View/Components/          # Componentes de vista Blade
├── config/
│   └── platforms.php             # Mapeo de plataformas a servicios
├── database/
│   ├── factories/                # Factories para pruebas
│   ├── migrations/               # Migraciones de base de datos
│   └── seeders/                  # Seeders de datos
├── resources/
│   └── views/                    # Vistas Blade
├── routes/
│   ├── web.php                   # Rutas principales
│   └── auth.php                  # Rutas de autenticación
└── tests/                        # Tests unitarios y de feature
```

---

## Arquitectura del Sistema

### Patrón de Diseño: Service Pattern

El núcleo de la aplicación utiliza el **Patrón de Servicios** con la interfaz `SendsMessages`:

```
┌─────────────────────┐
│  config/platforms.php│  ← Mapeo: nombre_plataforma → ClaseServicio
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│  SendMessage.php     │  ← Controlador invocable
│  (POST /send)        │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│  app(config['        │
│  platforms'][$name]) │  ← Resolución del servicio desde el contenedor DI
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│  SendsMessages       │  ← Interfaz común
│  ::sendMassMessage() │
└──────────┬──────────┘
           │
     ┌─────┴─────┬─────────┬──────────┐
     ▼           ▼         ▼          ▼
┌─────────┐ ┌─────────┐ ┌──────────┐ ┌──────────┐
│ Discord  │ │  Slack  │ │ Telegram │ │ WhatsApp │
│ Service  │ │ Service │ │ Service  │ │ Service  │
│(simulado)│ │(simulado)│ │ (API real)│ │ (API real)│
└────┬─────┘ └────┬────┘ └────┬─────┘ └────┬─────┘
     │            │           │             │
     └────────────┴─────┬─────┴─────────────┘
                        ▼
              ┌─────────────────┐
              │  Message::      │
              │  create()       │  ← Registro en base de datos
              └─────────────────┘
```

### Flujo de Envío de Mensajes

1. **Usuario** selecciona plataforma, destinatarios y escribe un mensaje en el Dashboard
2. **SendMessage** controlador valida los datos (plataforma válida, usuarios existen, mensaje no vacío)
3. Se resuelve el **servicio de plataforma** desde `config('platforms')` usando el contenedor DI
4. El servicio ejecuta `sendMassMessage()` que itera sobre cada usuario
5. Para cada usuario, se ejecuta `sendMessage()`:
   - **Discord/Slack**: Solo registra en logs y crea registro en BD con status "sent"
   - **Telegram**: Realiza petición HTTP a `https://api.telegram.org/bot{token}/sendMessage`
   - **WhatsApp**: Realiza petición HTTP a la Evolution API
6. Se crea un registro en la tabla `messages` con el resultado (sent/failed)

---

## Base de Datos

### Tabla `users`

| Campo | Tipo | Descripción |
|---|---|---|
| id | bigint | Clave primaria |
| name | string | Nombre del usuario |
| email | string | Email único del usuario |
| email_verified_at | timestamp | Fecha de verificación del email |
| password | string | Contraseña hasheada |
| telegram_chat_id | string (nullable, unique) | ID del chat de Telegram |
| whatsapp_number | string (nullable, unique) | Número de WhatsApp (solo dígitos) |
| remember_token | string | Token para "Recordarme" |
| created_at | timestamp | Fecha de creación |
| updated_at | timestamp | Fecha de última actualización |

### Tabla `messages`

| Campo | Tipo | Descripción |
|---|---|---|
| id | bigint | Clave primaria |
| platform | string(20) | Plataforma de envío (discord, slack, telegram, whatsapp) |
| message | text | Contenido del mensaje |
| status | string(20) | Estado del envío (sent, failed) |
| user_id | bigint (FK) | Usuario remitente |
| recipient_id | bigint (FK) | Usuario destinatario |
| created_at | timestamp | Fecha de envío |
| updated_at | timestamp | Fecha de última actualización |

### Tablas Adicionales (Laravel)

- `password_reset_tokens`: Tokens para restablecimiento de contraseña
- `sessions`: Sesiones de usuario (driver de sesión: database)
- `cache` / `cache_locks`: Sistema de caché
- `jobs` / `job_batches` / `failed_jobs`: Sistema de colas de trabajos

---

## Rutas de la Aplicación

### Rutas Principales (web.php)

| Método | Ruta | Controlador | Nombre | Descripción |
|---|---|---|---|---|
| GET | / | Closure → dashboard | dashboard | Página principal con formulario de envío |
| GET | /profile | ProfileController@edit | profile.edit | Formulario de edición de perfil |
| PATCH | /profile | ProfileController@update | profile.update | Actualizar información del perfil |
| DELETE | /profile | ProfileController@destroy | profile.destroy | Eliminar cuenta de usuario |
| GET | /sent | GetSentMessages | sent | Historial de mensajes enviados |
| POST | /send | SendMessage | send | Procesar envío de mensajes |
| GET | /users | UserController@index | users.index | Lista de usuarios registrados |
| POST | /users | UserController@store | users.store | Crear nuevo usuario |

### Rutas de Autenticación (auth.php)

| Método | Ruta | Controlador | Nombre | Descripción |
|---|---|---|---|---|
| GET | /register | RegisteredUserController@create | register | Formulario de registro |
| POST | /register | RegisteredUserController@store | - | Procesar registro |
| GET | /login | AuthenticatedSessionController@create | login | Formulario de login |
| POST | /login | AuthenticatedSessionController@store | - | Procesar login |
| POST | /logout | AuthenticatedSessionController@destroy | logout | Cerrar sesión |
| GET | /forgot-password | PasswordResetLinkController@create | password.request | Formulario "Olvidé contraseña" |
| POST | /forgot-password | PasswordResetLinkController@store | password.email | Enviar enlace de reseteo |
| GET | /reset-password/{token} | NewPasswordController@create | password.reset | Formulario nueva contraseña |
| POST | /reset-password | NewPasswordController@store | password.store | Guardar nueva contraseña |
| GET | /verify-email | EmailVerificationPromptController | verification.notice | Página de verificación de email |
| GET | /verify-email/{id}/{hash} | VerifyEmailController | verification.verify | Verificar email con enlace |
| POST | /email/verification-notification | EmailVerificationNotificationController@store | verification.send | Reenviar email de verificación |
| GET | /confirm-password | ConfirmablePasswordController@show | password.confirm | Formulario confirmar contraseña |
| POST | /confirm-password | ConfirmablePasswordController@store | - | Procesar confirmación |
| PUT | /password | PasswordController@update | password.update | Actualizar contraseña |

---

## Servicios de Mensajería

### Interfaz `SendsMessages`

```php
interface SendsMessages
{
    public function sendMessage(User $user, $message);
    public function sendMassMessage(array $users, $message);
}
```

Todos los servicios de plataforma deben implementar esta interfaz.

### DiscordService (Simulado)

- **Archivo**: `app/Services/DiscordService.php`
- **Comportamiento**: Registra el mensaje en los logs de Laravel y crea un registro en la base de datos con status "sent".
- **No realiza llamadas HTTP reales**.

### SlackService (Simulado)

- **Archivo**: `app/Services/SlackService.php`
- **Comportamiento**: Idéntico a DiscordService. Registra en logs y guarda en BD.
- **No realiza llamadas HTTP reales**.

### TelegramService (API Real)

- **Archivo**: `app/Services/TelegramService.php`
- **API**: `https://api.telegram.org/bot{token}/sendMessage`
- **Variables de entorno**: `TELEGRAM_BOT_TOKEN`
- **Requisito**: El usuario debe tener `telegram_chat_id` configurado
- **Comportamiento**: Realiza petición POST a la API de Telegram. Registra "sent" o "failed" según la respuesta.

### WhatsappService (Evolution API Real)

- **Archivo**: `app/Services/WhatsappService.php`
- **API**: `{EVOLUTION_API_URL}/message/sendText/{EVOLUTION_API_INSTANCE}`
- **Variables de entorno**:
  - `EVOLUTION_API_URL`: URL base de la Evolution API
  - `EVOLUTION_API_INSTANCE`: Nombre de la instancia
  - `EVOLUTION_API_KEY`: Clave de autenticación
- **Requisito**: El usuario debe tener `whatsapp_number` configurado
- **Comportamiento**: Limpia el número (solo dígitos), realiza petición POST con headers de autenticación. Registra "sent" o "failed".

---

## Variables de Entorno Requeridas

### Para Telegram

```env
TELEGRAM_BOT_TOKEN=tu_token_de_bot_de_telegram
```

### Para WhatsApp (Evolution API)

```env
EVOLUTION_API_URL=https://tu-evolution-api.com
EVOLUTION_API_INSTANCE=nombre_instancia
EVOLUTION_API_KEY=tu_api_key
```

---

## Configuración de Plataformas

El archivo `config/platforms.php` mapea cada nombre de plataforma a su clase de servicio:

```php
return [
    'discord'  => \App\Services\DiscordService::class,
    'slack'    => \App\Services\SlackService::class,
    'telegram' => \App\Services\TelegramService::class,
    'whatsapp' => \App\Services\WhatsappService::class,
];
```

### Cómo agregar una nueva plataforma

1. Crear una nueva clase de servicio en `app/Services/` que implemente `SendsMessages`
2. Agregar la entrada al archivo `config/platforms.php`:
   ```php
   'nueva_plataforma' => \App\Services\NuevaPlataformaService::class,
   ```
3. Si la plataforma requiere identificador del usuario, agregar una migración para el campo correspondiente en la tabla `users`
4. Actualizar el formulario de creación de usuarios en `resources/views/users/index.blade.php`

---

## Vistas Blade

### Layouts

- **`layouts/app.blade.php`**: Layout principal para páginas autenticadas. Incluye navegación y encabezado opcional.
- **`layouts/guest.blade.php`**: Layout para páginas de invitados (login, registro, etc.). Diseño centrado.
- **`layouts/navigation.blade.php`**: Barra de navegación con enlaces a Dashboard, Sent, Users. Dropdown con Profile y Logout.

### Páginas Principales

- **`dashboard.blade.php`**: Formulario de envío de mensajes. Selector de plataforma, checkboxes de usuarios, campo de mensaje.
- **`sent.blade.php`**: Tabla con historial de mensajes enviados: plataforma, destinatario, mensaje, estado.
- **`users/index.blade.php`**: Gestión de usuarios. Formulario para agregar (nombre, email, telegram, whatsapp) y tabla de usuarios.

### Páginas de Autenticación

- **`auth/login.blade.php`**: Formulario de inicio de sesión con "Recordarme" y "Olvidé contraseña".
- **`auth/register.blade.php`**: Formulario de registro con nombre, email y contraseña.
- **`auth/forgot-password.blade.php`**: Formulario para solicitar enlace de reseteo.
- **`auth/reset-password.blade.php`**: Formulario para establecer nueva contraseña.
- **`auth/verify-email.blade.php`**: Página de verificación de email.
- **`auth/confirm-password.blade.php`**: Confirmación de contraseña para áreas seguras.

### Páginas de Perfil

- **`profile/edit.blade.php`**: Página de edición con tres secciones: información, cambio de contraseña, eliminación de cuenta.
- **`profile/partials/update-profile-information-form.blade.php`**: Formulario para actualizar nombre y email.
- **`profile/partials/update-password-form.blade.php`**: Formulario para cambiar contraseña.
- **`profile/partials/delete-user-form.blade.php`**: Modal para eliminar cuenta.

---

## Testing

### Configuración

- Framework: **Pest PHP**
- Base de datos de pruebas: SQLite (en memoria)
- Trait: `RefreshDatabase` para tests de Feature

### Tests Disponibles

| Archivo | Tests | Descripción |
|---|---|---|
| `tests/Unit/ExampleTest.php` | 1 | Test unitario básico |
| `tests/Feature/ExampleTest.php` | 1 | Verifica que GET / retorna 200 |
| `tests/Feature/ProfileTest.php` | 5 | Gestión de perfil |
| `tests/Feature/Auth/AuthenticationTest.php` | 4 | Login y logout |
| `tests/Feature/Auth/RegistrationTest.php` | 2 | Registro de usuarios |
| `tests/Feature/Auth/PasswordResetTest.php` | 4 | Reseteo de contraseña |
| `tests/Feature/Auth/PasswordUpdateTest.php` | 2 | Actualización de contraseña |
| `tests/Feature/Auth/PasswordConfirmationTest.php` | 3 | Confirmación de contraseña |
| `tests/Feature/Auth/EmailVerificationTest.php` | 3 | Verificación de email |

### Ejecutar Tests

```bash
# Ejecutar todos los tests
./vendor/bin/pest

# Ejecutar tests de una categoría específica
./vendor/bin/pest tests/Feature/Auth/

# Ejecutar con verbose output
./vendor/bin/pest --verbose
```

---

## Seeders y Datos de Prueba

El `DatabaseSeeder` crea:

- **10 usuarios** aleatorios con UserFactory
- **1 usuario administrador**: `admin@example.com` / contraseña: `admin`
- **10 mensajes** por cada usuario con destinatarios aleatorios (diferentes al remitente)

### Ejecutar Seeders

```bash
# Ejecutar todos los seeders
php artisan db:seed

# Ejecutar con migraciones
php artisan migrate:fresh --seed
```

---

## Inicio Rápido

### 1. Instalar dependencias

```bash
composer install
npm install
```

### 2. Configurar entorno

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Configurar base de datos

Editar `.env` con la configuración de MySQL o usar SQLite:

```env
DB_CONNECTION=sqlite
# DB_DATABASE=/ruta/a/database/database.sqlite
```

### 4. Ejecutar migraciones y seeders

```bash
php artisan migrate --seed
```

### 5. Iniciar servidor de desarrollo

```bash
# Con Laravel Sail (Docker)
./vendor/bin/sail up

# O con artisan
php artisan serve

# Compilar assets
npm run dev
```

### 6. Acceder a la aplicación

- URL: `http://localhost:8000`
- Login admin: `admin@example.com` / `admin`

---

## Consideraciones de Seguridad

- Las contraseñas se almacenan hasheadas con bcrypt
- Las rutas sensibles requieren autenticación
- La eliminación de cuenta requiere contraseña actual
- Rate limiting en login (5 intentos)
- Verificación de email opcional
- Tokens CSRF en todos los formularios
- Variables de entorno para credenciales de API (nunca en código)

---

## Estructura de Archivos Documentados

Todos los archivos PHP del proyecto han sido documentados en español con PHPDoc blocks:

- **Servicios**: `SendsMessages.php`, `DiscordService.php`, `SlackService.php`, `TelegramService.php`, `WhatsappService.php`
- **Modelos**: `User.php`, `Message.php`
- **Controladores**: `Controller.php`, `SendMessage.php`, `GetSentMessages.php`, `UserController.php`, `ProfileController.php`
- **Requests**: `ProfileUpdateRequest.php`, `LoginRequest.php`
- **Factories**: `UserFactory.php`, `MessageFactory.php`
- **Seeders**: `DatabaseSeeder.php`
- **Migraciones**: Todas las migraciones de la base de datos
- **Rutas**: `web.php`, `auth.php`
- **Configuraciones**: `platforms.php`
- **Componentes**: `AppLayout.php`, `GuestLayout.php`
- **Providers**: `AppServiceProvider.php`
