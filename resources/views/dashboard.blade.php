
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    @if(session()->has('success'))
        <p class="p-4 bg-green-100 text-green-800">{{ session()->get('success') }}</p>
    @endif

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="post" action="{{ route('send') }}">
                        @csrf
                        <div class="space-y-4">
                            <div class="flex flex-col" x-data="{ platform: '{{ old('platform', 'email') }}' }">
                                <label for="platform">Platform</label>
                                <select name="platform" id="platform" x-model="platform">
                                    @foreach(config('platforms') as $platform => $_)
                                        <option value="{{ $platform }}">
                                            {{ \Illuminate\Support\Str::title($platform) }}
                                        </option>
                                    @endforeach
                                </select>
                                
                                <div class="mt-4" x-show="platform === 'whatsapp'">
                                    <label for="whatsapp_instance_id" class="block text-sm font-medium text-gray-700">WhatsApp Instance</label>
                                    <select name="whatsapp_instance_id" id="whatsapp_instance_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">{{ __('Default (.env)') }}</option>
                                        @foreach(auth()->user()->whatsappInstances as $instance)
                                            <option value="{{ $instance->id }}" {{ $instance->status !== 'connected' ? 'disabled' : '' }}>
                                                {{ $instance->name }} ({{ $instance->status }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">Only connected instances can send messages.</p>
                                </div>

                                @error('platform')
                                <p class="text-red-800">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex flex-col">
                                @forelse(\App\Models\User::where('id', '<>', auth()->id())->get() as $user)
                                    <div>
                                        <label for="user{{ $user->id }}">
                                            <input
                                                type="checkbox"
                                                name="users[]"
                                                value="{{ $user->id }}"
                                                id="user{{ $user->id }}"
                                            >
                                            {{ $user->name }}
                                        </label>
                                    </div>
                                @empty
                                    <p>No users to sent to.</p>
                                @endforelse

                                @error('users')
                                <p class="text-red-800">{{ $message }}</p>
                                @enderror
                                @error('users.*')
                                <p class="text-red-800">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex flex-col">
                                <label for="message">Message</label>
                                <input
                                    type="text"
                                    name="message"
                                    id="message"
                                >
                            </div>
                            @error('message')
                            <p class="text-red-800">{{ $message }}</p>
                            @enderror

                            <div>
                                <input
                                    class="bg-indigo-700 text-white px-4 py-2 rounded"
                                    type="submit"
                                    value="Submit"
                                >
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
