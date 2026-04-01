<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit User') }}: {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('User Information') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ __("Update the user's name, email, and social identifiers.") }}
                            </p>
                        </header>

                        <form method="post" action="{{ route('users.update', $user) }}" class="mt-6 space-y-6 max-w-xl">
                            @csrf
                            @method('patch')

                            <div>
                                <x-input-label for="name" :value="__('Name')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('email')" />
                            </div>

                            <div>
                                <x-input-label for="telegram_chat_id" :value="__('Telegram Chat ID')" />
                                <x-text-input id="telegram_chat_id" name="telegram_chat_id" type="text" class="mt-1 block w-full" :value="old('telegram_chat_id', $user->telegram_chat_id)" />
                                <p class="mt-1 text-sm text-gray-500">Find your ID using @userinfobot on Telegram.</p>
                                <x-input-error class="mt-2" :messages="$errors->get('telegram_chat_id')" />
                            </div>

                            <div>
                                <x-input-label for="whatsapp_number" :value="__('WhatsApp Number')" />
                                <x-text-input id="whatsapp_number" name="whatsapp_number" type="text" class="mt-1 block w-full" :value="old('whatsapp_number', $user->whatsapp_number)" />
                                <p class="mt-1 text-sm text-gray-500">Include country code (e.g., 584121234567).</p>
                                <x-input-error class="mt-2" :messages="$errors->get('whatsapp_number')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Update User') }}</x-primary-button>
                                <a href="{{ route('users.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline uppercase font-bold tracking-widest">
                                    {{ __('Cancel') }}
                                </a>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
