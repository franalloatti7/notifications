<x-modal name="modal-show" id="modal-show" focusable>
    <div class="p-6">
        @csrf

        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Notificación') }}
        </h2>

        <div class="mt-6">
            <div class="grid grid-rows-4 grid-flow-col gap-4">
                <div class="col-span-2">
                    <x-input-label for="tag" value="{{ __('Tag') }}" />
                </div>
                <div class="col-span-2">
                    <b id="tag-show"></b>
                </div>
                <div class="col-span-2">
                    <x-input-label for="message" value="{{ __('Mensaje') }}" />
                </div>
                <div class="col-span-2">
                    <b id="message-show"></b>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')" id="modal-show-cancel">
                {{ __('Cerrar') }}
            </x-secondary-button>
        </div>
    </div>
</x-modal>