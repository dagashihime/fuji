<x-app-layout>
    <div class="container mx-auto grid grid-cols-6 gap-2">
        @foreach($animeListItems as $item) 
            <span class="flex flex-col">
                <img src="{{ $item['anime']['main_picture'] }}" loading="lazy" class="w-36 aspect-[7/10] object-cover">
                <span class="flex flex-col">
                    <b>{{ $item['anime']['title'] }}</b>
                    <span class="flex">
                        {{ __('status.'.$item['status']) }}
                    </span>
                </span>
            </span>
        @endforeach
    </div>
</x-app-layout>