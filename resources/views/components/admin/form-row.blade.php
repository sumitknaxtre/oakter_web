@props(['cols' => 2])

@php
    $colClass = match ((int) $cols) {
        1 => 'admin-form-row--1',
        3 => 'admin-form-row--3',
        default => 'admin-form-row--2',
    };
@endphp

<div {{ $attributes->class(['admin-form-row', $colClass]) }}>
    {{ $slot }}
</div>
