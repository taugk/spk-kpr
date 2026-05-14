<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('admin.partials.head')
</head>
<body>
    @include('admin.partials.right-sidebar')
    @include('admin.partials.header')
    @include('admin.partials.sidebar')

    <div class="main-container">
        <div class="pd-ltr-20">
            @include('admin.components.alert')

            @hasSection('page_header')
                @yield('page_header')
            @else
                @include('admin.components.page-header', [
                    'title' => trim($__env->yieldContent('title')) ?: 'Dashboard',
                    'breadcrumbs' => $breadcrumbs ?? []
                ])
            @endif

            @yield('content')

            @include('admin.partials.footer')
        </div>
    </div>

    @include('admin.partials.scripts')
</body>
</html>
