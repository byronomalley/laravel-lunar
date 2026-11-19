<?php

use App\Mail\AwaitingPayment;
use App\Mail\OrderDispatched;
use App\Mail\PaymentOffline;
use App\Mail\PaymentReceived;
use Lunar\Models\Currency;
use Lunar\Models\Order;

dataset('order status mailers', [
    'awaiting payment' => ['awaiting-payment', AwaitingPayment::class, 'Order received - awaiting payment'],
    'payment offline' => ['payment-offline', PaymentOffline::class, 'Order received - offline payment'],
    'payment received' => ['payment-received', PaymentReceived::class, 'Payment received'],
    'dispatched' => ['dispatched', OrderDispatched::class, 'Order Dispatched'],
]);

it('configures a mailer for each order status', function (string $status, string $mailer) {
    expect(config("lunar.orders.statuses.{$status}.mailers"))
        ->toContain($mailer);
})->with('order status mailers');

it('renders the order reference, subject, and additional content', function (string $status, string $mailer, string $subject) {
    Currency::factory()->create(['code' => 'GBP']);
    $order = Order::factory()->create();
    $content = 'Your parcel has been delayed by one day.';

    $mailable = new $mailer($order, $content);

    $mailable->assertHasSubject($subject)
        ->assertSeeInHtml($order->reference)
        ->assertSeeInHtml($content);
})->with('order status mailers');

it('omits additional content when none is provided', function (string $status, string $mailer) {
    Currency::factory()->create(['code' => 'GBP']);
    $order = Order::factory()->create();

    $mailable = new $mailer($order);

    $mailable->assertDontSeeInHtml('Additional notes');
})->with('order status mailers');
