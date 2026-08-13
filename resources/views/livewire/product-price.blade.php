<?php

use Livewire\Volt\Component;
use Lunar\Facades\Pricing;
use Lunar\Models\Price;

new class extends Component {
    public ?Price $price = null;
    public $product;

    public function mount($product)
    {
        $this->product = $product;

        $variant = $this->product->variants->first();
        if ($variant) {
            $this->price = Pricing::for($variant)->get()->matched;
        }
    }
} ?>

<span>
    {{ $this->price?->price->formatted() }}
</span>
