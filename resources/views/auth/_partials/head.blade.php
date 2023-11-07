<head>
    <meta charset="utf-8" />
    <title>{{ $data['title'] }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Management System" />
    <meta name="author" content="Ghaly Fadhillah" />
    <meta name="developer" content="Ghaly Fadhillah" />
    <meta name="robots" content="noindex, nofollow" />
    <meta name="googlebot" content="noindex" />
    <meta name="googlebot-news" content="nosnippet">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link href="{{ asset('app') }}/assets/css/styles.css" rel="stylesheet" type="text/css" />
    <link rel="shortcut icon" href="{{ asset('logo') }}/fav.png" />
    <link href="{{ asset('cdn/select2.min.css') }}" rel="stylesheet" type="text/css" >
    <link href="{{ asset('cdn/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
</head>