@props(['genres'])
<span class="px-1 py-3 flex flex-wrap justify-center gap-1">
    @foreach ($genres as $genre)
        <span class="px-1 bg-blue-300 rounded-full text-xs">
            {{ $genre['title'] }}
        </span>
    @endforeach
</span>