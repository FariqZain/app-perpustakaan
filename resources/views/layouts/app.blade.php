<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Perpustakaan')</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        nav { display: flex; align-items: center; justify-content: space-between; gap: 20px; background: #0f172a; color: #e2e8f0; padding: 14px 24px; }
        .brand { font-weight: 700; font-size: 1.1rem; }
        nav ul { display: flex; align-items: center; gap: 16px; list-style: none; padding: 0; margin: 0; }
        nav ul li a, nav a { color: #cbd5e1; text-decoration: none; }
        nav ul li a.active, nav a.active { color: #fff; font-weight: 700; }
        nav .navbar-user { display: flex; align-items: center; gap: 12px; color: #cbd5e1; font-size: 14px; }
        nav .btn-logout { background: none; border: 1px solid #cbd5e1; color: #cbd5e1; padding: 4px 10px; border-radius: 4px; cursor: pointer; font-size: 14px; }
        nav .btn-logout:hover { background: #1e40af; color: #fff; }
        .container { padding: 24px; }
        table { border-collapse: collapse; width: 100%; margin-top: 16px; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px 12px; text-align: left; }
        th { background: #f3f4f6; }
        form.inline { display: inline; }
        .btn { display: inline-block; padding: 6px 12px; background: #2563eb; color: #fff; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; }
        .alert-success { background: #dcfce7; color: #15803d; padding: 10px 14px; border-radius: 4px; margin-bottom: 16px; }
        .alert-error { background: #fee2e2; color: #b91c1c; padding: 10px 14px; border-radius: 4px; margin-bottom: 16px; }
        .badge { display: inline-block; padding: 3px 8px; font-size: 12px; font-weight: 600; border-radius: 9999px; text-transform: capitalize; }
        .badge-success { background-color: #dcfce7; color: #15803d; }
        .badge-warning { background-color: #fef3c7; color: #b45309; }
        .badge-danger { background-color: #fee2e2; color: #b91c1c; }
        .card { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; max-width: 700px; }
        label { display: block; margin-top: 12px; font-weight: bold; }
        input { width: 100%; padding: 8px; margin-top: 4px; box-sizing: border-box; }
        .error { color: #b91c1c; font-size: 14px; margin-top: 4px; }
    </style>
</head>
<body>
    @include('partials.navbar')

    <div class="container">
        @yield('content')
    </div>
</body>
</html>