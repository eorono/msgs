
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
                            <div class="flex flex-col">
                                <label for="platform">Platform</label>
                                <select name="platform" id="platform">
                                    @foreach(config('platforms') as $platform => $_)
                                        <option value="{{ $platform }}">
                                            {{ \Illuminate\Support\Str::title($platform) }}
                                        </option>
                                    @endforeach
                                </select>
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
