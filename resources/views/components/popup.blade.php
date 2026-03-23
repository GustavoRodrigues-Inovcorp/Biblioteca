@props([
    'id',
    'color' => 'green', // green, blue, red, yellow, etc
    'icon' => null, // SVG ou null para usar o default
    'title',
    'message',
    'showCancel' => false,
    'okText' => 'OK',
    'cancelText' => 'Cancelar',
    'onOk' => null, // JS function name or null
    'onCancel' => null, // JS function name or null
    'messageClass' => '' // Classe extra para o texto da mensagem
])

@php
    $colorMap = [
        'green' => [
            'border' => 'border-green-400',
            'icon' => 'text-green-500',
            'title' => 'text-green-600',
            'msg' => 'text-green-700',
            'btn' => 'bg-green-500 hover:bg-green-600',
        ],
        'blue' => [
            'border' => 'border-blue-400',
            'icon' => 'text-blue-500',
            'title' => 'text-blue-600',
            'msg' => 'text-blue-700',
            'btn' => 'bg-blue-500 hover:bg-blue-600',
        ],
        'red' => [
            'border' => 'border-red-400',
            'icon' => 'text-red-500',
            'title' => 'text-red-600',
            'msg' => 'text-red-700',
            'btn' => 'bg-red-500 hover:bg-red-600',
        ],
        'yellow' => [
            'border' => 'border-yellow-400',
            'icon' => 'text-yellow-500',
            'title' => 'text-yellow-600',
            'msg' => 'text-yellow-700',
            'btn' => 'bg-yellow-500 hover:bg-yellow-600',
        ],
    ];
    $c = $colorMap[$color] ?? $colorMap['green'];
@endphp

<div id="{{ $id }}" class="fixed top-0 left-0 w-full h-full flex items-center justify-center z-50" style="display:none;" onclick="event.stopPropagation();">
    <div class="absolute inset-0 bg-black bg-opacity-60" onclick="event.stopPropagation();"></div>
    <div class="relative bg-white rounded-lg shadow-xl border {{ $c['border'] }} p-6 max-w-md w-full animate-fade-in">
        <div class="flex items-center mb-4">
            @if ($icon)
                {!! $icon !!}
            @else
                <svg class="w-8 h-8 {{ $c['icon'] }} mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            @endif
            <span class="text-lg font-semibold {{ $c['title'] }}">{{ $title }}</span>
        </div>
        <div class="{{ $c['msg'] }} mb-6 text-center break-words leading-relaxed max-w-[90%] mx-auto {{ $messageClass }}">{!! $message !!}</div>
        <div class="flex justify-center gap-4">
            <button onclick="{{ $onOk ?: 'closePopup(\''.$id.'\')' }}" class="{{ $c['btn'] }} text-white font-medium px-4 py-2 rounded transition min-w-[100px] text-xs">{{ $okText }}</button>
            @if ($showCancel)
                <button onclick="{{ $onCancel ?: 'closePopup(\''.$id.'\')' }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium px-4 py-2 rounded transition min-w-[100px] text-xs">{{ $cancelText }}</button>
            @endif
        </div>
    </div>
    <style>
        .animate-fade-in { animation: fadeIn .3s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    </style>
</div>
<script>
    function closePopup(id) {
        document.getElementById(id).style.display = 'none';
    }
</script>
