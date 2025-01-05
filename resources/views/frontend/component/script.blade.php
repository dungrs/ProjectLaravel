@php
    $scripts = [
        asset('frontend/resources/plugins/wow/dist/wow.min.js'),
        asset('frontend/resources/uikit/js/uikit.min.js'),
        'https://unpkg.com/swiper/swiper-bundle.min.js',
        asset('frontend/resources/uikit/js/components/sticky.min.js'),
        asset('frontend/resources/function.js'),
        'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/js/jquery.nice-select.min.js'
    ];
@endphp

@foreach ($scripts as $script)
    <script src="{{ $script }}"></script>
@endforeach
