<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="author" content="{{ $systems['homepage_company'] }}" />
<meta name="copyright" content="{{ $systems['homepage_company'] }}" />
<link rel="icon" href="{{ $systems['homepage_favicon'] }}" type="image/png" sizes="30x30" />
<title>{{ $seo['meta_title'] }}</title>
<meta name="description" content="{{ $seo['meta_description'] }}" />
<meta name="keyword" content="{{ $seo['meta_keyword'] }}" />
<link rel="canonical" href="{{ $seo['canonical'] }}" />
<meta property="og:locale" content="vi_VN" />

<meta property="og:title" content="{{ $seo['meta_title'] }}" />
<meta property="og:type" content="website" />
<meta property="og:image" content="{{ $seo['meta_image'] }}" />
<meta property="og:url" content="{{ $seo['canonical'] }}" />
<meta property="og:description" content="{{ $seo['meta_description'] }}" />
<meta property="og:site_name" content="" />
<meta property="fb:admins" content="" />
<meta property="app_id" content="" />
<meta property="twitter:card" content="" />
<meta property="twitter:title" content="{{ $seo['meta_title'] }}" />
<meta property="twitter:description" content="{{ $seo['meta_description'] }}" />
<meta property="twitter:image" content="{{ $seo['meta_image'] }}" />
@php
    $stylesheets = [
        asset('frontend/resources/fonts/font-awesome-4.7.0/css/font-awesome.min.css'),
        asset('frontend/resources/uikit/css/uikit.modify.css'),
        'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
        'https://unpkg.com/swiper/swiper-bundle.min.css',
        'https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/css/nice-select.css',
        asset('frontend/resources/library/css/library.css'),
        asset('frontend/resources/plugins/wow/css/libs/animate.css'),
        asset('frontend/resources/style.css')
    ];

    $scripts = [
        asset('frontend/resources/library/js/jquery.js'),
    ];
@endphp

@foreach ($stylesheets as $stylesheet)
    <link rel="stylesheet" href="{{ $stylesheet }}" />
@endforeach

@foreach ($scripts as $script)
    <script src="{{ $script }}"></script>
@endforeach