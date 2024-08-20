<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <script type="module" crossorigin src="/vendor/luminix/cms/mui-cms.js"></script>
    <link rel="stylesheet" crossorigin href="/vendor/luminix/cms/style.css">
</head>
<body>
    @luminixEmbed()
    <div id="root"></div>
</body>
</html>