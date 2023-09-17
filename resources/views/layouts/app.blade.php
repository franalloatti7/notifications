<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"/>
    
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
        <script>
            var pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
                cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
                encrypted: true
            });
            var myTags = @json(auth()->user()->tags->pluck('name'));
            myTags.forEach(function(tag) {
                console.log('subscripto a ' + tag);
                var channel = pusher.subscribe(tag);
                channel.bind('App\\Events\\NotifyEvent', function(data) {
                    getMyNotifications();
                });
            });
            function publish() {
                if(document.getElementById('tag').value == ''){
                    toastr.error('Debe seleccionar un tag');
                    return;
                }
                $('#modal-publish-cancel').click();
                document.body.style.cursor = 'wait';
                var token = $("input[name='_token']")[0].value;
                const params = {
                    tag: document.getElementById('tag').value,
                    message: document.getElementById('message').value,
                    _token: token,
                    method: 'get',
                };
                const queryString = new URLSearchParams(params).toString();

                const apiUrl = '{{ env("APP_URL") }}/events/publish?'+queryString;
        
                fetch(apiUrl)
                    .then(response => {
                        if (!response.ok) {
                            toastr.error('La solicitud GET no fue exitosa');
                            document.body.style.cursor = 'default';
                        }
                        return response.json();
                    })
                    .then(data => {
                        document.body.style.cursor = 'default';
                        document.getElementById('tag').value = ''
                        document.getElementById('message').value = ''
                        toastr.success(data.message);
                    })
                    .catch(error => {
                        document.body.style.cursor = 'default';
                        toastr.error(error);
                    });
            }
            function getMyNotifications(){
                var token = $("input[name='_token']")[0].value;
                const params = {
                    _token: token,
                    method: 'get',
                };
                const queryString = new URLSearchParams(params).toString();

                const apiUrl = '{{ env("APP_URL") }}/notifications/getMyNotifications?'+queryString;
        
                fetch(apiUrl)
                    .then(response => {
                        if (!response.ok) {
                            toastr.error('La solicitud GET no fue exitosa');
                        }
                        return response.json();
                    })
                    .then(data => {
                        var unreadnotifications = data.unread;
                        var html_unread = '';
                        $('#notifications_content').html('');
                        $('#count_notifications').html(unreadnotifications.length);
                        unreadnotifications.forEach(function(notification) {
                            $('#notifications_content').append(`<x-dropdown-link style=" background-color: beige; border-radius: 30px; contain: paint;">
                            <div style="display: flex">
                                <div style="width: 95%;">
                                    <b style="font-size: large;cursor: pointer;" x-data="" x-on:click.prevent="$dispatch('open-modal', 'modal-show'); markRead('`+notification.id+`', '`+notification.data.tag+`', '`+notification.data.message+`');">`+notification.data.tag+`</b>
                                </div>
                                <div>
                                    <span style="font-size: large;cursor: pointer;" aria-hidden="true" style="font-size: x-large" href="/notifications.delete/`+notification.id+`" onclick="return confirm('Está seguro/a?')">
                                        &times;</span>
                                </div>
                            </div>
                            <p>`+notification.data.message.substr(0,30)+(notification.data.message.length > 30 ? '...' : '')+`</p>
                            <span class="ml-3 float-right text-muted text-xs">`+notification.createdAt+`</span>
                        </x-dropdown-link>`);
                        });
                        let html_divider = '<div class="dropdown-divider" style="border-bottom-width: thick;"></div>';
                        $('#notifications_content').append(html_divider);
                        var readnotifications = data.read;
                        var html_read = '';
                        readnotifications.forEach(function(notification) {
                            $('#notifications_content').append(`<x-dropdown-link style="background-color: aliceblue; border-radius: 30px;contain: paint;">
                            <div style="display: flex">
                                <div style="width: 95%;">
                                    <b style="font-size: large">`+notification.data.tag+`</b>
                                </div>
                                <div>
                                    <span aria-hidden="true" style="font-size: x-large" href="/notifications/delete/`+notification.id+`" onclick="return confirm('Está seguro/a?')">
                                        &times;</span>
                                </div>
                            </div>
                            <p>`+notification.data.message.substr(0,30)+(notification.data.message.length > 30 ? '...' : '')+`</p>
                            <span class="ml-3 float-right text-muted text-xs">`+notification.createdAt+`</span></x-dropdown-link>`);
                        });
                    })
                    .catch(error => {
                        toastr.error(error);
                    });
            }
            function markRead(id, tag, message){
                $('#tag-show').html(tag);
                $('#message-show').html(message);
                var token = $("input[name='_token']")[0].value;
                const params = {
                    _token: token,
                    method: 'get',
                };
                const queryString = new URLSearchParams(params).toString();

                const apiUrl = '{{ env("APP_URL") }}/notifications/markread/'+id+'?'+queryString;
        
                fetch(apiUrl)
                    .then(response => {
                        if (!response.ok) {
                            toastr.error('La solicitud GET no fue exitosa');
                        }
                        return response.json();
                    })
                    .then(data => {
                        getMyNotifications();
                    })
                    .catch(error => {
                        toastr.error(error);
                    });
            }
        </script>


        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif
            <!-- Page Content -->
            <main>
                <script>
                    @if(Session::has('message'))
                        toastr.success("{{ Session::get('message') }}");
                    @endif
                    @if(Session::has('error'))
                        toastr.error("{{ Session::get('error') }}");
                    @endif
                    @if(Session::has('info'))
                        toastr.info("{{ Session::get('info') }}");
                    @endif
                    @if(Session::has('warning'))
                        toastr.warning("{{ Session::get('warning') }}");
                    @endif
                </script>
                {{ $slot }}
            </main>
            <x-modal name="modal-publish" id="modal-publish" focusable>
                <div class="p-6">
                    @csrf
        
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Publicar evento') }}
                    </h2>
        
                    <div class="mt-6">
                        <div class="grid grid-rows-4 grid-flow-col gap-4">
                            <div class="col-span-2">
                                <x-input-label for="tag" value="{{ __('Tag') }}" />
                            </div>
                            <div class="col-span-2">
                                <select class="px-4 py-3 rounded-full" style="width: 300px" name="tag" id="tag">
                                    <option value="">Seleccione un tag</option>
                                    @foreach ($all_tags as $tag)
                                        <option value="{{ $tag->name }}">
                                            {{ $tag->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <div>
                                    <x-input-label for="message" value="{{ __('Mensaje') }}" />
                                </div>
                                <div>
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
        </div>
        <script>
            getMyNotifications();
        </script>
    </body>
</html>
