<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Traits\FetchesUrls;
use Illuminate\View\View;
use Lunar\Models\Collection as CollectionModel;

new
#[Layout('components.layouts.app')]
class extends Component
{
    use FetchesUrls;

    public function mount(string $slug = ''): void
    {
        $this->url = $this->fetchUrl(
            $slug,
            (new CollectionModel)->getMorphClass(),
            [
                'element.thumbnail',
                'element.products.variants.basePrices',
                'element.products.defaultUrl',
            ]
        );

        if (! $this->url) {
            abort(404);
        }
    }

    public function getCollectionProperty(): mixed
    {
        return $this->url->element;
    }
} ?>

<section>
    <div class="max-w-screen-xl px-4 py-12 mx-auto sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold">
            {{ $this->collection->translateAttribute('name') }}
        </h1>

        <div class="grid grid-cols-1 gap-8 mt-8 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($this->collection->products as $product)
                <x-product-card :product="$product" />
            @empty
            @endforelse
        </div>
    </div>
</section>
