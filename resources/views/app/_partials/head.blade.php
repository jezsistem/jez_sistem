<head><base href="">
    <meta charset="utf-8" />
    <title>{{ $data['title'] }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Topscore Management System" />
    <meta name="author" content="Ghaly Fadhillah" />
    <meta name="developer" content="Ghaly Fadhillah" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="google" content="notranslate">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
    />
    <link href="{{ asset('app') }}/assets/plugins/custom/fullcalendar/fullcalendar.bundle.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('app') }}/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
	<link href="{{ asset('app') }}/assets/plugins/custom/prismjs/prismjs.bundle.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('app') }}/assets/css/style.bundle.css?v1" rel="stylesheet" type="text/css" />
    <link href="{{ asset('app') }}/assets/css/themes/layout/header/base/light.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('app') }}/assets/css/themes/layout/aside/dark.css" rel="stylesheet" type="text/css" />

    <link rel="shortcut icon" href="{{ asset('logo') }}/fav.png" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('cdn/jquery.toast.min.css') }}" rel="stylesheet" type="text/css" >

    <!-- DatePicker -->
    <link href="{{ asset('app') }}/assets/css/daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css" />

</head>