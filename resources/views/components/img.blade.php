@props(['src'])
<img 
    src="{{ $src }}" 
    loading="lazy" 
    {{ $attributes->merge(['class'=> 'aspect-[7/10] object-cover rounded-lg']) }}
/>
