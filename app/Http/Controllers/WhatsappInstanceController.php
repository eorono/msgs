<?php

namespace App\Http\Controllers;

use App\Models\WhatsappInstance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador para la gestión de instancias de WhatsApp (Multi-instancia).
 *
 * Permite crear, listar, conectar (vía QR) y eliminar instancias
 * de WhatsApp utilizando la Evolution API.
 */
class WhatsappInstanceController extends Controller
{
    /**
     * URL base de la Evolution API.
     */
    protected $apiUrl;

    /**
     * Clave de API global.
     */
    protected $apiKey;

    /**
     * Inicializa las credenciales de la API desde el entorno.
     */
    public function __construct()
    {
        $this->apiUrl = env('EVOLUTION_API_URL');
        $this->apiKey = env('EVOLUTION_API_KEY');
    }

    /**
     * Muestra la lista de instancias del usuario.
     */
    public function index()
    {
        $instances = Auth::user()->whatsappInstances;
        return view('whatsapp.index', compact('instances'));
    }

    /**
     * Muestra el formulario para crear una nueva instancia.
     */
    public function create()
    {
        return view('whatsapp.create');
    }

    /**
     * Registra una nueva instancia en la base de datos y en la Evolution API.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|alpha_dash|unique:whatsapp_instances,name',
        ]);

        // Intentar crear la instancia en la Evolution API
        $response = Http::withHeaders([
            'apikey' => $this->apiKey,
        ])->post("{$this->apiUrl}/instance/create", [
            'instanceName' => $request->name,
            'qrcode' => true,
        ]);

        if ($response->successful()) {
            WhatsappInstance::create([
                'name' => $request->name,
                'instance_id' => $request->name,
                'user_id' => Auth::id(),
                'status' => 'disconnected',
            ]);

            return redirect()->route('whatsapp.index')->with('success', 'Instancia creada exitosamente.');
        }

        return back()->withErrors(['api' => 'Error de la API: ' . ($response->json('message') ?? $response->body())]);
    }

    /**
     * Muestra el estado de conexión y el código QR de una instancia.
     */
    public function show(WhatsappInstance $whatsappInstance)
    {
        // Consultar estado actual en la API
        $statusResponse = Http::withHeaders([
            'apikey' => $this->apiKey,
        ])->get("{$this->apiUrl}/instance/connectionStatus/{$whatsappInstance->name}");

        $qr = null;
        $state = $statusResponse->json('instance.state') ?? 'disconnected';

        // Si no está abierta, intentamos obtener el QR
        if ($state !== 'open') {
            $qrResponse = Http::withHeaders([
                'apikey' => $this->apiKey,
            ])->get("{$this->apiUrl}/instance/connect/{$whatsappInstance->name}");
            
            if ($qrResponse->successful()) {
                $qr = $qrResponse->json('code'); // El base64 del QR
            }
        }

        // Actualizar estado en nuestra BD
        $whatsappInstance->update(['status' => $state === 'open' ? 'connected' : 'disconnected']);

        return view('whatsapp.show', [
            'instance' => $whatsappInstance,
            'state' => $state,
            'qr' => $qr
        ]);
    }

    /**
     * Elimina una instancia de la base de datos y de la Evolution API.
     */
    public function destroy(WhatsappInstance $whatsappInstance)
    {
        // Intentar eliminar en la API (usamos logout por seguridad primero o delete directo)
        Http::withHeaders([
            'apikey' => $this->apiKey,
        ])->delete("{$this->apiUrl}/instance/delete/{$whatsappInstance->name}");

        $whatsappInstance->delete();

        return redirect()->route('whatsapp.index')->with('success', 'Instancia eliminada correctamente.');
    }
}
