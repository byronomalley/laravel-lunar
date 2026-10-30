{{-- resources/views/mail/layouts/brand.blade.php --}}
@php
    $brand = [
        'primary' => '#1f2937',   // buttons, header bar
        'accent'  => '#c2410c',
        'text'    => '#374151',
        'muted'   => '#6b7280',
        'border'  => '#e5e7eb',
        'bg'      => '#f3f4f6',
        'font'    => "'Helvetica Neue', Helvetica, Arial, sans-serif", // web fonts are unreliable in email
    ];
@endphp
    <!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>@yield('title', config('app.name'))</title>
</head>
<body style="margin:0;padding:0;background:{{ $brand['bg'] }};font-family:{{ $brand['font'] }};color:{{ $brand['text'] }};">

{{-- Hidden preview text shown in the inbox list --}}
<div style="display:none;max-height:0;overflow:hidden;opacity:0;">@yield('preheader')</div>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:{{ $brand['bg'] }};">
    <tr><td align="center" style="padding:24px 12px;">

            <table role="presentation" width="600" cellpadding="0" cellspacing="0"
                   style="width:100%;max-width:600px;background:#ffffff;border-radius:8px;overflow:hidden;">

                {{-- Header --}}
                <tr>
                    <td align="center" style="background:{{ $brand['primary'] }};padding:24px;">
                        <x-brand.logo-link />
                    </td>
                </tr>

                {{-- Body --}}
                <tr>
                    <td style="padding:32px 32px 8px;font-size:16px;line-height:24px;">
                        @yield('content')
                    </td>
                </tr>

                {{-- Footer --}}
                <tr>
                    <td style="padding:24px 32px 32px;border-top:1px solid {{ $brand['border'] }};font-size:13px;line-height:20px;color:{{ $brand['muted'] }};">
                        Questions? Reply to this email or visit
                        <a href="{{ config('app.url') }}/contact" style="color:{{ $brand['accent'] }};">our help page</a>.<br>
                        &copy; {{ now()->year }} {{ config('app.name') }}
                    </td>
                </tr>
            </table>

        </td></tr>
</table>
</body>
</html>
