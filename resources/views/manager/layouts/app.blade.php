<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('manager.partials.head')
</head>
<body>
    @include('manager.partials.right-sidebar')
    @include('manager.partials.header')
    @include('manager.partials.sidebar')

    <div class="main-container">
        <div class="pd-ltr-20">
            @include('manager.components.alert')



            @yield('content')

            @include('manager.partials.footer')
        </div>
    </div>

    @include('manager.partials.scripts')
</body>
</html>
