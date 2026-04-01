<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('WhatsApp Instances') }}
            </h2>
            <a href="{{ route('whatsapp.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-sm transition ease-in-out duration-150">
                {{ __('Add New Instance') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($instances as $instance)
                            <div class="border rounded-lg p-5 shadow-sm hover:shadow-md transition-shadow duration-200">
                                <div class="flex justify-between items-start mb-4">
                                    <h3 class="text-lg font-bold text-gray-800 truncate">{{ $instance->name }}</h3>
                                    <span class="px-2 py-1 text-xs rounded-full {{ $instance->status === 'connected' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($instance->status) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mb-4">ID: {{ $instance->instance_id }}</p>
                                
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('whatsapp.show', $instance) }}" class="flex-1 text-center bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold py-2 px-4 rounded transition duration-150">
                                        {{ $instance->status === 'connected' ? __('View Status') : __('Link Phone') }}
                                    </a>
                                    
                                    <form method="POST" action="{{ route('whatsapp.destroy', $instance) }}" onsubmit="return confirm('¿Estás seguro de eliminar esta instancia? Se desconectará de WhatsApp.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 p-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-10 text-center text-gray-500">
                                {{ __('No instances found. Add one to start sending WhatsApp messages.') }}
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
