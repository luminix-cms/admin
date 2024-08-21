<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <script type="module" crossorigin src="https://unpkg.com/@luminix/mui-cms@0.0.1-beta.1/bundle/mui-cms.bundle.iife.js"></script>
    <link rel="stylesheet" crossorigin href="https://unpkg.com/@luminix/mui-cms@0.0.1-beta.1/bundle/style.css">
</head>
<body>
    @luminixEmbed()
    <div id="root"></div>
</body>
</html>