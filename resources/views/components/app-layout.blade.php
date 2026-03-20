{{-- resources/views/components/app-layout.blade.php --}}
@props(['header' => null])

@include('layouts.app', ['header' => $header, 'slot' => $slot])
