<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Tags') }}
        </h2>
    </x-slot>
 
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="overflow-hidden overflow-x-auto border-b border-gray-200 bg-white p-6">
                    @if(Auth::user()->role == 'admin')
                    <a href="{{ route('tags.create') }}"
                       class="mb-4 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                        Crear
                    </a>
                    @endif
                    <div class="min-w-full align-middle">
                        <table class="min-w-full border divide-y divide-gray-200">
                            <thead>
                            <tr>
                                <th class="bg-gray-50 px-6 py-3 text-left">
                                    <span class="text-xs font-medium uppercase leading-4 tracking-wider text-gray-500">Nombre</span>
                                </th>
                                <th class="w-57 bg-gray-50 px-6 py-3 text-left">
                                </th>
                            </tr>
                            </thead>
 
                            <tbody class="bg-white divide-y divide-gray-200 divide-solid">
                                @foreach($tags as $tag)
                                    <tr class="bg-white">
                                        <td class="px-6 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                                            {{ $tag->name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm leading-5 text-gray-900 whitespace-no-wrap">
                                            @if($tag->user_id == null) 
                                                <form action="{{ route('tags.subscribe', $tag->id) }}" method="POST" onsubmit="return confirm('Esta seguro/a de subscribirse?')" style="display: inline-block;">
                                            @else
                                                <form action="{{ route('tags.unsubscribe', $tag->id) }}" method="POST" onsubmit="return confirm('Esta seguro/a de desubscribirse?')" style="display: inline-block;">
                                            @endif
                                                @csrf
                                                @method('POST')
                                            <button style="background-color: @if($tag->user_id == null) #35c124 @else #d9cc33 @endif; color:white;"
                                            class="inline-flex items-center rounded-md border border-gray-300 bg-green px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                                               @if($tag->user_id == null)
                                               Subscribirse
                                           @else
                                               Desubscribirse
                                           @endif
                                            </button>
                                            </form>
                                            @if(Auth::user()->role == 'admin')
                                            <a href="{{ route('tags.edit', $tag->id) }}"
                                               class="inline-flex items-center rounded-md border border-gray-300 bg-green px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25">
                                                Editar
                                            </a>
                                            <form action="{{ route('tags.destroy', $tag->id) }}" method="POST" onsubmit="return confirm('Esta seguro/a?')" style="display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <x-danger-button>
                                                    Eliminar
                                                </x-danger-button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $tags->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>