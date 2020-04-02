<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Oauth</title>

        <!-- Fonts -->

        <!-- Styles -->
    </head>
    <body>
        <div class="app" id="app">
            <p>{{ asset('js/app.js')}}</p>
            <app></app>
        </div>
    </body>
    <script type="text/javascript" src="{{ mix('js/app.js') }}">
    </script>
</html>
