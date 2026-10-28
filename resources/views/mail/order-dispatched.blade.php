{{-- resources/views/mail/order-dispatched.blade.php --}}
@extends('components.layouts.mail')

@section('title', 'Your order is on the way')
@section('preheader', "Order {$order->reference} has been dispatched")

@section('content')
    <p style="margin:0 0 24px;">Your order <strong>{{ $order->reference }}</strong> has been dispatched.</p>

    @if($content ?? null)
        <div style="margin:0 0 24px;padding:16px;background:#f9fafb;border-left:4px solid #c2410c;">
            <strong>Additional notes</strong><br>
            {!! nl2br(e($content)) !!}
        </div>
    @endif

    @include('mail.partials.order', ['order' => $order])

    <p style="margin:32px 0;text-align:center;">
        <a href="{{ url('/account/orders/'.$order->id) }}"
           style="display:inline-block;padding:12px 28px;background:#1f2937;color:#ffffff;text-decoration:none;border-radius:6px;font-weight:bold;">
            View your order
        </a>
    </p>
@endsection
