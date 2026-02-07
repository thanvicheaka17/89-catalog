{{-- Dropdown Component --}}
@php
    $dropdownId = $attributes->get('id', 'dropdown-' . uniqid());
    $dropdownClass = $attributes->get('class', '');
    $triggerClass = $attributes->get('trigger-class', 'dropdown-toggle');
    $menuClass = $attributes->get('menu-class', 'dropdown-menu');
    $triggerText = $attributes->get('trigger', 'Actions');
    $hasArrow = !str_contains($dropdownClass, 'sidebar-dropdown');
@endphp

<div class="dropdown {{ $dropdownClass }}" id="{{ $dropdownId }}">
    <button type="button"
            class="{{ $triggerClass }}"
            onclick="toggleDropdownMenu('{{ $dropdownId }}')"
            {{ $attributes->except(['id', 'class', 'trigger-class', 'menu-class', 'trigger']) }}>
        {{ $triggerText }}
        @if($hasArrow)
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="dropdown-arrow">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
        </svg>
        @endif
    </button>

    <div class="{{ $menuClass }}">
        {{ $slot }}
    </div>
</div>
