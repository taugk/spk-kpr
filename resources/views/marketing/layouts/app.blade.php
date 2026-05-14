<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('marketing.partials.head')
</head>
<body>
    @include('marketing.partials.right-sidebar')
    @include('marketing.partials.header')
    @include('marketing.partials.sidebar')

    <div class="main-container">
        <div class="pd-ltr-20">
            @include('marketing.components.alert')



            @yield('content')

            @include('marketing.partials.footer')
        </div>
    </div>

    @include('marketing.partials.scripts')
</body>
</html>
