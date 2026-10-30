<?php

use Livewire\Volt\Volt;
use Lunar\DataTypes\Price;
use Lunar\DataTypes\ShippingOption;
use Lunar\Facades\ShippingManifest;
use Lunar\Models\Cart;
use Lunar\Models\Country;
use Lunar\Models\Order;
use Lunar\Models\TaxClass;
use Lunar\Models\TaxZone;

function currentCheckoutCart(): Cart
{
    $cart = Cart::factory()->create();

    session()->put(config('lunar.cart_session.session_key'), $cart->id);

    return $cart;
}

function checkoutAddress(array $overrides = []): array
{
    return array_merge([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'line_one' => '1 Main Street',
        'city' => 'London',
        'postcode' => 'SW1A 1AA',
        'country_id' => Country::factory()->create()->id,
        'contact_email' => 'john@example.com',
    ], $overrides);
}

function checkoutShippingOption(Cart $cart): ShippingOption
{
    TaxZone::factory()->create(['default' => true, 'active' => true]);

    $option = new ShippingOption(
        name: 'Standard Delivery',
        description: 'Standard delivery',
        identifier: 'standard',
        price: new Price(500, $cart->currency, 1),
        taxClass: TaxClass::factory()->create(),
    );

    ShippingManifest::addOption($option);

    return $option;
}

it('redirects home when there is no cart', function () {
    Volt::test('checkout-page')->assertRedirect('/');
});

it('starts at the shipping address step for a new cart', function () {
    currentCheckoutCart();

    Volt::test('checkout-page')
        ->assertSet('currentStep', 1)
        ->assertSee('Shipping Details');
});

it('continues from the payment step when all details are saved', function () {
    config(['lunar.payments.default' => 'offline']);

    $cart = currentCheckoutCart();
    $option = checkoutShippingOption($cart);
    $address = checkoutAddress();

    $cart->setShippingAddress($address);
    $cart->setBillingAddress($address);
    $cart->setShippingOption($option);

    Volt::test('checkout-page')
        ->assertSet('currentStep', 4)
        ->assertSet('chosenShipping', 'standard')
        ->assertSet('shipping.first_name', 'John')
        ->assertSet('billing.first_name', 'John');
});

it('saves the shipping address and copies it to billing', function () {
    $cart = currentCheckoutCart();

    Volt::test('checkout-page')
        ->set('shipping', checkoutAddress())
        ->call('saveAddress', 'shipping')
        ->assertHasNoErrors()
        ->assertSet('currentStep', 2);

    $cart->refresh();

    expect($cart->shippingAddress)->not->toBeNull()
        ->and($cart->billingAddress)->not->toBeNull()
        ->and($cart->shippingAddress->first_name)->toBe('John')
        ->and($cart->billingAddress->first_name)->toBe('John');
});

it('does not copy the shipping address to billing when disabled', function () {
    $cart = currentCheckoutCart();

    Volt::test('checkout-page')
        ->set('shippingIsBilling', false)
        ->set('shipping', checkoutAddress())
        ->call('saveAddress', 'shipping')
        ->assertHasNoErrors();

    $cart->refresh();

    expect($cart->shippingAddress)->not->toBeNull()
        ->and($cart->billingAddress)->toBeNull();
});

it('requires the shipping address fields', function () {
    $cart = currentCheckoutCart();

    Volt::test('checkout-page')
        ->call('saveAddress', 'shipping')
        ->assertHasErrors([
            'shipping.first_name' => 'required',
            'shipping.line_one' => 'required',
            'shipping.country_id' => 'required',
            'shipping.city' => 'required',
            'shipping.postcode' => 'required',
            'shipping.contact_email' => 'required',
        ]);

    expect($cart->refresh()->shippingAddress)->toBeNull();
});

it('escapes quotes in the saved address', function () {
    $cart = currentCheckoutCart();

    Volt::test('checkout-page')
        ->set('shipping', checkoutAddress(['first_name' => "O'Brien"]))
        ->call('saveAddress', 'shipping')
        ->assertHasNoErrors();

    expect($cart->refresh()->shippingAddress->first_name)->toBe("O\\'Brien");
});

it('returns only the supported countries', function () {
    currentCheckoutCart();

    Country::factory()->create(['iso3' => 'GBR']);
    Country::factory()->create(['iso3' => 'USA']);
    Country::factory()->create(['iso3' => 'FRA']);

    $countries = Volt::test('checkout-page')->instance()->countries;

    expect($countries->pluck('iso3')->sort()->values()->all())->toBe(['GBR', 'USA']);
});

it('saves the chosen shipping option and moves to billing', function () {
    $cart = currentCheckoutCart();
    $cart->setShippingAddress(checkoutAddress());
    checkoutShippingOption($cart);

    Volt::test('checkout-page')
        ->call('saveShippingOption')
        ->assertHasNoErrors()
        ->assertSet('currentStep', 3);

    expect($cart->refresh()->shippingAddress->shipping_option)->toBe('standard');
});

it('creates an offline order and redirects to the success page', function () {
    config([
        'lunar.payments.default' => 'offline',
        'lunar.payments.types.offline' => ['driver' => 'offline', 'authorized' => 'payment-offline'],
    ]);

    $cart = currentCheckoutCart();
    $address = checkoutAddress();
    $cart->setShippingAddress($address);
    $cart->setBillingAddress($address);

    Volt::test('checkout-page')
        ->call('checkout')
        ->assertRedirect(route('checkout-success.view'));

    $order = Order::where('cart_id', $cart->id)->whereNotNull('placed_at')->first();

    expect($order)->not->toBeNull()
        ->and($order->status)->toBe('payment-offline');
});
