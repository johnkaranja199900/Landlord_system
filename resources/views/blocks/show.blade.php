<x-layout :title="'Block '.$block->name">
    <h1>{{ $block->name }}</h1>
    <p class="muted">{{ $block->property->name }}</p>
</x-layout>
