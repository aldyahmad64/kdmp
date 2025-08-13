@php
    if (!isset($record) || !is_object($record)) {
        echo '<span class="text-gray-400 italic">-</span>';
        return;
    }
    $type = data_get($record, 'type');
    $value = data_get($record, 'value');
@endphp

@switch($type)
    @case('text')
        {{ $value }}
    @break

    @case('boolean')
        {!! $value == '1' || strtolower($value) === 'aktif' ? '✅ Aktif' : '❌ Tidak aktif' !!}
    @break

    @case('gambar')
        <img src="{{ asset('storage/' . $value) }}" alt="Gambar" class="w-10 h-10 rounded">
    @break

    @case('warna')
        <div class="inline-flex items-center gap-2">
            <div class="w-5 h-5 rounded-full border border-gray-300" style="background-color: {{ $value }};"></div>
            <span class="text-sm text-gray-700">{{ $value }}</span>
        </div>
    @break

    @case('richtext')
        Klik untuk detail
    @break

    @default
        {{ $value }}
@endswitch
