<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f9fafb;
            color: #111827;
        }


        .card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 2.5rem 3rem;
            text-align: center;
            max-width: 420px;
            width: 90%;
        }


        .code {
            font-size: 72px;
            font-weight: 500;
            letter-spacing: -3px;
            line-height: 1;
        }

        .divider {
            width: 40px;
            height: 2px;
            background: #e5e7eb;
            border-radius: 2px;
            margin: 1.25rem auto;
        }

        h1 {
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .sub {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            padding: 8px 20px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            background: transparent;
            color: inherit;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.15s;
        }

        .btn:hover {
            background: #f3f4f6;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="code">@yield('code')</div>
        <div class="divider"></div>
        <h1>@yield('title')</h1>
        <p class="sub">@yield('message')</p>
        <a href="{{ url('/') }}" class="btn">
            ← Go back home
        </a>
    </div>
</body>

</html>