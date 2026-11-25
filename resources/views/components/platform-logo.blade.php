@props(['platform' => 'tiktok', 'size' => 'md', 'class' => ''])

@php
    $dimensions = match($size) {
        '2xs' => ['box' => 16, 'icon' => 10, 'radius' => 4],
        'xs' => ['box' => 18, 'icon' => 11, 'radius' => 4],
        'sm' => ['box' => 26, 'icon' => 14, 'radius' => 6],
        'lg' => ['box' => 44, 'icon' => 22, 'radius' => 11],
        default => ['box' => 34, 'icon' => 18, 'radius' => 8], // 'md'
    };

    $pKey = strtolower($platform ?? 'tiktok');
@endphp

<div class="platform-logo-squircle {{ $class }}" style="width: {{ $dimensions['box'] }}px; height: {{ $dimensions['box'] }}px; border-radius: {{ $dimensions['radius'] }}px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 1px 3px rgba(0,0,0,0.12);
    @if($pKey === 'tiktok')
        background: linear-gradient(135deg, #111827 0%, #030712 100%); color: #ffffff;
    @elseif($pKey === 'shopee')
        background: linear-gradient(135deg, #ee4d2d 0%, #ff5722 100%); color: #ffffff;
    @elseif($pKey === 'tokopedia')
        background: linear-gradient(135deg, #03ac0e 0%, #00950b 100%); color: #ffffff;
    @elseif($pKey === 'lazada')
        background: linear-gradient(135deg, #0f146d 0%, #1e2998 100%); color: #ffffff;
    @else
        background: linear-gradient(135deg, #475569 0%, #334155 100%); color: #ffffff;
    @endif
">
    @if($pKey === 'tiktok')
        <svg viewBox="0 0 24 24" width="{{ $dimensions['icon'] }}" height="{{ $dimensions['icon'] }}" fill="currentColor">
            <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.89 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1v-3.52a6.37 6.37 0 0 0-.79-.05A6.34 6.34 0 0 0 3 15.67 6.34 6.34 0 0 0 9.34 22a6.34 6.34 0 0 0 6.34-6.33V9.08a8.27 8.27 0 0 0 3.91 1.05v-3.44a4.85 4.85 0 0 1 0-.01z"/>
        </svg>
    @elseif($pKey === 'shopee')
        <svg viewBox="0 0 24 24" width="{{ $dimensions['icon'] }}" height="{{ $dimensions['icon'] }}" fill="currentColor">
            <path d="M19 6h-2c0-2.76-2.24-5-5-5S7 3.24 7 6H5c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-7-3c1.66 0 3 1.34 3 3h-6c0-1.66 1.34-3 3-3zm7 17H5V8h2v2c0 .55.45 1 1 1s1-.45 1-1V8h6v2c0 .55.45 1 1 1s1-.45 1-1V8h2v12zm-8.8-4.7c.3.2.7.4 1.2.5.4.1.8.2 1.2.3.6.1 1.1.3 1.4.6.4.3.5.7.5 1.2 0 .6-.2 1.1-.7 1.4-.5.4-1.2.6-2 .6-.6 0-1.2-.1-1.8-.4-.6-.2-1-.6-1.3-1l1.1-1.1c.3.3.6.6 1 .7.4.2.8.3 1.1.3.4 0 .7-.1.9-.2.2-.2.3-.4.3-.6 0-.2-.1-.4-.3-.5-.2-.1-.6-.3-1.1-.4-.5-.1-1-.3-1.4-.4-.4-.2-.8-.4-1.1-.7-.3-.3-.4-.7-.4-1.2 0-.6.2-1.1.7-1.5.5-.4 1.1-.6 1.9-.6.6 0 1.1.1 1.6.3.5.2.9.5 1.2.8l-1.1 1.1c-.2-.2-.5-.4-.8-.5-.3-.1-.7-.2-1-.2-.4 0-.7.1-.9.2-.2.1-.3.3-.3.6 0 .2.1.3.3.5.2.1.5.2.9.3z"/>
        </svg>
    @elseif($pKey === 'tokopedia')
        <svg viewBox="0 0 24 24" width="{{ $dimensions['icon'] }}" height="{{ $dimensions['icon'] }}" fill="currentColor">
            <path d="M19 6h-2.18A4.99 4.99 0 0 0 12 2a4.99 4.99 0 0 0-4.82 4H5a2 2 0 0 0-2 2v11a3 3 0 0 0 3 3h12a3 3 0 0 0 3-3V8a2 2 0 0 0-2-2zm-7-2c1.63 0 3 1.34 3 3H9c0-1.66 1.37-3 3-3zm4.5 11.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm-9 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z"/>
        </svg>
    @elseif($pKey === 'lazada')
        <svg viewBox="0 0 24 24" width="{{ $dimensions['icon'] }}" height="{{ $dimensions['icon'] }}" fill="currentColor">
            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
        </svg>
    @else
        <svg xmlns="http://www.w3.org/2000/svg" width="{{ $dimensions['icon'] }}" height="{{ $dimensions['icon'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
    @endif
</div>
