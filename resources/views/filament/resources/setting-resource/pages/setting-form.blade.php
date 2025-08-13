<x-filament::page>
    <form wire:submit="save">
        {{ $this->form }}
        <x-filament::button form="save" type="submit" class="mt-4">
            Simpan
        </x-filament::button>
    </form>
</x-filament::page>
