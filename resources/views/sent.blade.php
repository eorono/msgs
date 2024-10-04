<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Sent') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <table class="table-auto w-full">
                    <thead>
                    <tr class="border-b border-black">
                        <th class="p-2">Platform</th>
                        <th class="p-2">Recipient</th>
                        <th class="p-2">Message</th>
                        <th class="p-2">Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($messages as $message)
                        <tr class="border-b">
                            <td class="p-2">{{ $message->platform }}</td>
                            <td class="p-2">{{ $message->recipient->name }}</td>
                            <td class="p-2">{{ $message->message }}</td>
                            <td class="p-2">{{ $message->status }}</td>
                        </tr>
                    @empty
                        <p>You have not sent any message</p>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
