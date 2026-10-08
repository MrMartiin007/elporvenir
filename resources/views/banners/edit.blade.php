<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $banner->tipo === 'video' ? __('Editar video de la portada') : __('Editar banner de la portada') }}
        </h2>
    </x-slot>

    <div class="py-1">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg bg-div">
                <div class="p-6 text-gray-900">
                    @include('banners._form', ['action' => route('banners.update', $banner), 'method' => 'PUT'])
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
