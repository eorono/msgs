<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Conectar WhatsApp') }}: {{ $instance->name }}
            </h2>
            <a href="{{ route('whatsapp.index') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-900 border-b-2 border-indigo-600 pb-1">
                {{ __('Volver a la Lista') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-center">
                    @if($qr)
                        <div class="mb-8">
                            <h3 class="text-2xl font-bold text-gray-800 mb-2">Escanea el código QR</h3>
                            <p class="text-gray-600 mb-6">Abre WhatsApp en tu teléfono, ve a Dispositivos vinculados y escanea el código.</p>
                            
                            <div class="inline-block p-4 bg-white border-4 border-indigo-600 rounded-xl shadow-xl">
                                <img src="{{ $qr }}" alt="WhatsApp QR Code" class="w-64 h-64 mx-auto">
                            </div>
                            
                            <p class="mt-8 text-sm text-gray-500 animate-pulse">Esperando conexión...</p>
                        </div>
                        
                        <div class="mt-10">
                            <button onclick="window.location.reload();" class="bg-gray-800 hover:bg-black text-white font-bold py-2 px-6 rounded transition duration-150">
                                {{ __('Actualizar Estado') }}
                            </button>
                        </div>
                    @elseif($state === 'open')
                        <div class="py-10">
                            <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <h3 class="text-3xl font-bold text-gray-800 mb-2">¡Conectado!</h3>
                            <p class="text-gray-600 italic">Tu instancia de WhatsApp está activa y lista para enviar mensajes.</p>
                        </div>
                    @else
                        <div class="py-10 text-red-500">
                            <p>{{ __('No se pudo obtener el QR. Por favor intenta recargar o crear la instancia de nuevo.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
