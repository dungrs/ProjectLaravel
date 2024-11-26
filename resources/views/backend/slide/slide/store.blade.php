@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['create']['title']])
@include('backend.dashboard.component.formError')
@php
    $url = ($config['method'] == 'create') ? route('slide.store') : route("slide.update", $slide->id)
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            @include('backend.slide.slide.component.list')
            @include('backend.slide.slide.component.aside')
        </div>

        @include('backend.dashboard.component.button');
    </div>
</form>