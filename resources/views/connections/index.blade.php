<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg flex flex-col">
                <div data-connectiontype="mal" class="flex">
                    @php
                        $connected = $connections->where('domain', 'https://api.myanimelist.net')->isNotEmpty();
                    @endphp
                    <a href="https://myanimelist.net/" target="_blank">
                        <img src="https://image.myanimelist.net/ui/OK6W_koKDTOqqqLDbIoPAiC8a86sHufn_jOI-JGtoCQ" class="h-32 aspect-square">
                    </a>
                    <div class="w-full p-4 grid grid-rows-2">
                        <span class="flex justify-between items-center">
                            <span>
                                <b>MyAnimeList</b>
                                @if ($connected)
                                    <p class="flex items-center gap-1">
                                        <x-heroicon-s-check class="h-5 p-0.5 bg-green-300 border rounded-full" />
                                        Your MyAnimeList account is connected
                                    </p>      
                                @else
                                    <p>
                                        Anime lists and such
                                    </p>
                                @endif

                            </span>
                            @if ($connected)
                                <button data-type="disconnect">Disconnect</button>
                            @else
                                <button data-type="connect">Connect</button>   
                            @endif

                        </span>
                        <p class="text-sm text-gray-700">
                            Anime and stuff
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @vite('resources/js/pages/connections.ts')
</x-app-layout>