<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New WhatsApp Instance') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 text-center">
                    <form method="POST" action="{{ route('whatsapp.store') }}" class="max-w-md mx-auto py-10 space-y-6">
                        @csrf
                        
                        <div>
                            <x-input-label for="name" :value="__('Nombre de la Instancia')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus placeholder="Ej: Soporte_Ventas" />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            <x-input-error class="mt-2" :messages="$errors->get('api')" />
                        </div>

                        <div class="flex items-center justify-end gap-4 mt-6">
                            <a href="{{ route('whatsapp.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline uppercase tracking-widest font-bold">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Crear Instancia') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
