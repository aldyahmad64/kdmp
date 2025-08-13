<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div>
        @php
            $data = is_array($getState()) ? Illuminate\Support\Arr::except($getState(), ['password','created_at','updated_at','deleted_at']) : [];
        @endphp

        @if (!empty($data))
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="w-full text-sm text-left table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            @foreach(array_keys($data) as $key)
                                <th class="px-4 py-2 font-medium text-gray-700 whitespace-nowrap">
                                    {{ Str::headline($key) }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white overflow-y-scroll">
                        <tr>
                            @foreach($data as $key => $value)
                                <td class="px-4 py-2 text-gray-800 whitespace-nowrap">
                                    @if(! Illuminate\Support\Str::contains($key, 'id'))
                                        {{ ($value === '1' || $value === '0' || $value === 1 || $value === 0) ? ($value ? 'true' : 'false') : (is_array($value) ? json_encode($value) : $value) }}
                                    @else
                                        {{ (is_array($value) ? json_encode($value) : $value) }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-dynamic-component>
