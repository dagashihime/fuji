<x-app-layout>
    <div class="container mx-auto p-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-8 gap-4">
        @foreach($animeListItems as $item) 
            <a href="{{ route('anime.show', [ 'id'=> $item['anime']['id'] ]) }}" class="group relative flex flex-col hover:scale-105 shadow-sm rounded-lg overflow-hidden">
                <div class="z-20 absolute inset-0 hidden group-hover:flex flex-col justify-between">
                    <span class="p-2 bg-slate-500 text-white bg-opacity-70">
                        <strong>{{ $item['anime']['title'] }}</strong>
                    </span>
                    <x-list.genre-wrapper :genres="$item['anime']['genres']" />
                </div>
                <img src="{{ $item['anime']['main_picture'] }}" loading="lazy" class="z-10 aspect-[7/10] object-cover rounded-b-lg">
                @php
                    $bg = __('color.status.'.$item['status'], locale: 'view');
                    $w = $item['num_episodes_watched'] / $item['anime']['num_episodes'] * 100
                @endphp
                <span class="relative -mt-2 h-3.5 bg-gray-300 rounded-b-lg overflow-hidden">
                    @if ($w > 0)
                        <span class="absolute inset-0 {{ $bg }}" style="width: {{ $w }}%"></span>
                    @endif
                </span>
            </a>
        @endforeach
    </div>
</x-app-layout>