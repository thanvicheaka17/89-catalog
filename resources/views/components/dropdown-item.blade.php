{{-- Dropdown Item Component --}}
@php
    $itemClass = 'dropdown-item';
    if (isset($danger) && $danger) {
        $itemClass .= ' danger';
    }
    $href = $attributes->get('href', '#');
    $isLink = $href !== '#';
@endphp

@if($isLink)
    <a href="{{ $href }}"
       class="{{ $itemClass }}"
       {{ $attributes->except(['href', 'danger']) }}>
        {{ $slot }}
    </a>
@else
    <button type="button"
            class="{{ $itemClass }}"
            {{ $attributes->except(['danger']) }}>
        {{ $slot }}
    </button>
@endif
