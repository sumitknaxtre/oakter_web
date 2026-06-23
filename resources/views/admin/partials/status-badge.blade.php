@php
    $class = match ($status) {
        'paid' => 'is-paid',
        'failed' => 'is-failed',
        default => 'is-pending',
    };
@endphp
<span class="admin-badge {{ $class }}">{{ $status }}</span>
