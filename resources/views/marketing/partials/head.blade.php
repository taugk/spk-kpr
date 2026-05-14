<meta charset="utf-8">
<title>@yield('title', 'Marketing SPK KPR') - {{ config('app.name', 'SPK KPR') }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('deskapp/vendors/images/apple-touch-icon.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('deskapp/vendors/images/favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('deskapp/vendors/images/favicon-16x16.png') }}">

<link href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,800,900&display=swap" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="{{ asset('deskapp/vendors/styles/core.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('deskapp/vendors/styles/icon-font.min.css') }}">

<link rel="stylesheet" type="text/css" href="{{ asset('deskapp/src/plugins/datatables/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('deskapp/src/plugins/datatables/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('deskapp/vendors/styles/style.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

@stack('styles')
