<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'UjianKu')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        img, video {
            max-width: 100%;
            height: auto;
        }
        .prose {
            overflow-wrap: anywhere;
        }
        .prose table {
            display: block;
            max-width: 100%;
            overflow-x: auto;
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 min-h-screen">
    @yield('content')
</body>
</html>