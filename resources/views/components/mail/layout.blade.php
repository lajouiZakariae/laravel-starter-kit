<html>

<head>
    <title>{{ $title ?? 'Mail from ' . config('app.name') }}</title>
</head>

<body>

    <div>
        {{ $slot }}

        <footer>
            <p>Copyright {{ date('Y') }} {{ config('app.name') }}</p>
            <p>All rights reserved.</p>
            <p>{{ config('app.name') }}</p>
        </footer>
    </div>
</body>

</html>