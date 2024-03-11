<x-app-layout>
    <div class="container mx-auto p-4 grid grid-cols-3 gap-2">
        <x-card>
            <x-img :src="$anime['main_picture']" /> 
            
            <strong class="text-xl">{{ $anime['title'] }}</strong>
            <span>
                <b>Aired:</b> {{ \Carbon\Carbon::parse($anime['start_date'])->format('M d, Y') }} to {{ \Carbon\Carbon::parse($anime['end_date'])->format('M d, Y') }}
            </span>
            @if($anime['genres'])
                <b class="text-center">Genres</b>
                <x-list.genre-wrapper :genres="$anime['genres']" />
            @endif
        </x-card>
        <x-card class="col-span-2">

        </x-card>
    </div>
</x-app-layout>