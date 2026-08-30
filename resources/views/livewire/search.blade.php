<?php

use Livewire\Volt\Component;
use Lunar\Models\Product;
use Lunar\Search\Facades\Search;

new class extends Component {

    public string $search = '';
    public $results = [];
    public bool $hasSearched = false;
    public bool $linesVisible = false;

    public function updatedSearch()
    {
        if (filled($this->search)) {
            $this->results = Product::search($this->search)->get();
            $this->hasSearched = true;
            $this->linesVisible = true;
        } else {
            $this->results = [];
            $this->hasSearched = false;
        }
    }
}
?>

<form class="relative"
      {{--      action="{{ route('search.view') }}"--}}
      x-data="{
         linesVisible: @entangle('linesVisible').live
     }">
    <input name="search"
           type="search"
           placeholder="Search for products"
           class="w-full pl-10 text-sm border-2 border-gray-100 rounded-lg"
           wire:model.live.debounce.150ms="search" {{-- Livewire handles timing and execution now --}}
           @click="linesVisible = true"
    />

    <button class="absolute p-2 text-gray-600 transition -translate-y-1/2 rounded-md left-1 top-1/2 hover:bg-gray-50">
        <span class="sr-only">Submit Search</span>

        <svg xmlns="http://www.w3.org/2000/svg"
             class="w-4 h-4"
             fill="none"
             viewBox="0 0 24 24"
             stroke="currentColor">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
    </button>

    <div
        class="absolute inset-x-0 top-auto z-50 w-screen max-w-sm px-6 py-8 mx-auto mt-4 bg-white border border-gray-100 shadow-xl sm:left-auto rounded-xl"
        x-show="$wire.search.length > 0 && linesVisible"
        x-on:click.away="linesVisible = false"
        x-transition
        x-cloak
    >
        <div wire:loading wire:target="search">
            <p class="text-sm text-gray-400">Searching…</p>
        </div>
        <div wire:loading.remove wire:target="search">
            @if($hasSearched && count($results))
                <ul class="flex flex-col gap-1">
                @foreach($results as $result)
                    <li>
                        <a href="{{ route('product.view', $result->defaultUrl->slug) }}" class="flex">
                            @if ($result->thumbnail)
                                <img class="object-cover w-1/3 overflow-hidden rounded-lg aspect-w-1 aspect-h-1"
                                     src="{{ $result->thumbnail->getUrl() }}"
                                     alt="{{ $result->translateAttribute('name') }}"
                                />
                            @endif
                            <p>{{ $result->variants->first()?->sku . ' ' . $result->translateAttribute('name') }}</p>
                        </a>
                    </li>
                @endforeach
                </ul>
            @elseif($hasSearched)
                <p>No results found.</p>
            @endif
        </div>
    </div>
</form>

