<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>

    @viteReactRefresh
    @vite(['resources/js/luminix-admin.jsx'])

</head>
<body>
    @luminixEmbed()
    <div id="root"></div>
</body>
</html>