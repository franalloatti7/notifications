<x-modal name="modal-publish" id="modal-publish" focusable>
    <div class="p-6">
        @csrf

        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Publicar evento') }}
        </h2>

        <div class="mt-6">
            <div class="grid grid-rows-4 grid-flow-col gap-4" style="height: 250px">
                <div class="col-span-4 pt-1">
                    <x-input-label for="tag" value="{{ __('Tag') }}" />
                    <select class="px-4 py-3 rounded-full" style="width: 300px" name="tag" id="tag">
                        <option value="">Seleccione un tag</option>
                        @foreach ($all_tags as $tag)
                            <option value="{{ $tag->id }}">
                                {{ $tag->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-4 pt-6">
                    <x-input-label for="message" value="{{ __('Mensaje') }}" />
                    <x-text-input
                        id="message"
                        name="message"
                        type="text"
                        class="mt-1 block w-3/4"
                        style="height: 100px;"
                    />
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')" id="modal-publish-cancel">
                {{ __('Cancelar') }}
            </x-secondary-button>

            <x-danger-button class="ml-3" onclick="publish();">
                {{ __('Publicar') }}
            </x-danger-button>
        </div>
    </div>
</x-modal>