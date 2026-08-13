<?php

use Livewire\Volt\Component;
use Lunar\Base\Purchasable;
use Lunar\Facades\CartSession;

new class extends Component {
    /**
     * The purchasable model we want to add to the cart.
     */
    public ?Purchasable $purchasable = null;

    /**
     * The quantity to add to cart.
     */
    public int $quantity = 1;

    public function rules(): array
    {
        return [
            'quantity' => 'required|numeric|min:1|max:10000',
        ];
    }

    public function addToCart(): void
    {
        $this->validate();

        if ($this->purchasable->stock < $this->quantity) {
            $this->addError('quantity', 'The quantity exceeds the available stock.');

            return;
        }

        CartSession::manager()->add($this->purchasable, $this->quantity);
        $this->dispatch('add-to-cart');
    }
} ?>

<div>
    <div class="flex gap-4">
        <div>
            <label for="quantity"
                   class="sr-only">
                Quantity
            </label>

            <input class="w-16 px-1 py-4 text-sm text-center transition border border-gray-100 rounded-lg no-spinner"
                   type="number"
                   id="quantity"
                   min="1"
                   value="1"
                   wire:model.live="quantity" />
        </div>

        <button type="submit"
                class="w-full px-6 py-4 text-sm font-medium text-center text-white bg-indigo-600 rounded-lg hover:bg-indigo-700"
                wire:click.prevent="addToCart">
            Add to Cart
        </button>
    </div>

    @if ($errors->has('quantity'))
        <div class="p-2 mt-4 text-xs font-medium text-center text-red-700 rounded bg-red-50"
             role="alert">
            @foreach ($errors->get('quantity') as $error)
                {{ $error }}
            @endforeach
        </div>
    @endif
</div>
