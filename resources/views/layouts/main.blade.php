<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mechanix D.I.Y.')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{--  Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&display=swap"
        rel="stylesheet"
    />

    {{--  Bootstrap CSS --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet"
    />

    {{--  Font Awesome --}}
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />

    {{--  Your main CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    <!-- reponsive -->
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}">

     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    {{-- Page-specific CSS (optional) --}}
    @stack('styles')
</head>
<body>

    {{--  SAME HEADER --}}
    @include('partials.header')

    {{--  PAGE CONTENT --}}
    @yield('content')

    {{--  SAME FOOTER --}}
    @include('partials.footer')

    {{--  Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Page-specific scripts (optional) --}}
    @stack('scripts')

</body>
</html>
