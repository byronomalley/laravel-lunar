<?php

    $th = "padding:8px 0;border-bottom:2px solid #e5e7eb;font-size:12px;text-transform:uppercase;letter-spacing:.05em;color:#6b7280;";
$td = "padding:12px 0;border-bottom:1px solid #e5e7eb;font-size:14px;vertical-align:top;";
$lines = $order->lines->where('type', '!=', 'shipping');
$shipping = $order->lines->where('type', 'shipping')->first();
$addresses = [
    'Delivery address' => $order->shippingAddress,
    'Billing address'  => $order->billingAddress,
];
?>

{{-- Summary --}}
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 24px;font-size:14px;">
    <tr>
        <td style="padding:2px 0;color:#6b7280;">Order reference</td>
        <td align="right"><strong>{{ $order->reference }}</strong></td>
    </tr>
    <tr>
        <td style="padding:2px 0;color:#6b7280;">Placed</td>
        <td align="right">{{ $order->placed_at?->format('j F Y') }}</td>
    </tr>
    @if($shipping)
        <tr>
            <td style="padding:2px 0;color:#6b7280;">Delivery method</td>
            <td align="right">{{ $shipping->description }}</td>
        </tr>
    @endif
</table>

{{-- Items --}}
<table role="presentation" width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <th align="left"  style="{{ $th }}">Item</th>
        <th align="center" style="{{ $th }}">Qty</th>
        <th align="right" style="{{ $th }}">Price</th>
        <th align="right" style="{{ $th }}">Total</th>
    </tr>
    @foreach($lines as $line)
        <tr>
            <td style="{{ $td }}">
                <strong>{{ $line->description }}</strong>
                @if($line->option)<br><span style="color:#6b7280;">{{ $line->option }}</span>@endif
                @if($line->identifier)<br><span style="color:#9ca3af;font-size:12px;">SKU {{ $line->identifier }}</span>@endif
            </td>
            <td align="center" style="{{ $td }}">{{ $line->quantity }}</td>
            <td align="right"  style="{{ $td }}">{{ $line->unit_price->formatted() }}</td>
            <td align="right"  style="{{ $td }}">{{ $line->sub_total->formatted() }}</td>
        </tr>
    @endforeach
</table>
