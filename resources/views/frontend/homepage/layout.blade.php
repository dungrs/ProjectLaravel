<!DOCTYPE html>
<html lang="en">
<head>
    @include('frontend.component.head')
    <title>Document</title>
</head>
<body>
    @include('frontend.component.header')
    @yield('content')
    @include('frontend.component.footer')
    @include('frontend.component.script')
</body>
</html>