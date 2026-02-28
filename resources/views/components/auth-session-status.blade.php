@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'ec-alert ec-alert-success']) }}>
        {{ $status }}
    </div>
@endif

