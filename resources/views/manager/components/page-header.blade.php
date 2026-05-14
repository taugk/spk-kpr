<div class="page-header">
    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="title">
                <h4>{{ $title ?? 'Dashboard' }}</h4>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('manager.dashboard') }}">Admin</a></li>
                    @foreach(($breadcrumbs ?? []) as $label => $url)
                        @if($loop->last || empty($url))
                            <li class="breadcrumb-item active" aria-current="page">{{ $label }}</li>
                        @else
                            <li class="breadcrumb-item"><a href="{{ $url }}">{{ $label }}</a></li>
                        @endif
                    @endforeach
                </ol>
            </nav>
        </div>
        <div class="col-md-6 col-sm-12 text-right">
            @yield('page_action')
        </div>
    </div>
</div>
