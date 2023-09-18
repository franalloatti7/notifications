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
            var myTags = @json(auth()->user()->tags);
            myTags.forEach(function(tag) {
                console.log('subscripto a ' + tag.name);
                var channel = pusher.subscribe(tag.id+'-channel');
                channel.bind('App\\Events\\NotifyEvent', function(data) {
                    getMyNotifications();
                });
            });
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
                @include('layouts.flash')
                {{ $slot }}
            </main>
            @include('layouts.modal-publish')
            @include('layouts.modal-show')
        </div>
        <script>
            getMyNotifications();
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
    </body>
</html>
