<?php

use Livewire\Volt\Component;
use Lunar\Models\Product;

new class extends Component {
    public string $search = '';
    public $results = [];
    public bool $linesVisible = false;

    public function query()
    {
//        dd($this->search);
        $this->results = Product::search($this->search)
            ->where('status', 'published')
            ->get()
        ;

        $this->linesVisible = true;
    }
}
?>

<form class="relative"
    {{--      action="{{ route('search.view') }}"--}}
    x-data="{
         linesVisible: @entangle('linesVisible').live
     }">
    <input name="query"
           type="search"
           placeholder="Search for products"
           class="w-full pl-10 text-sm border-2 border-gray-100 rounded-lg"
           wire:model.live="search"
           wire:change.debounce.300="query"
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

        <div class="absolute inset-x-0 top-auto z-50 w-screen max-w-sm px-6 py-8 mx-auto mt-4 bg-white border border-gray-100 shadow-xl sm:left-auto rounded-xl"
             x-show="$wire.search.length > 0 && linesVisible"
             x-on:click.away="linesVisible = false"
             x-transition
             x-cloak>
            @if($results)
            @foreach($results as $result)
                <a href="{{ route('product.view', $result->defaultUrl->slug) }} }}">{{$result->variants->first()?->sku . ' ' . $result->translateAttribute('name')}}</a>
            @endforeach
            @else
                <p>No Results</p>
            @endif
        </div>
</form>

