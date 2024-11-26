@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo'][$config['method']]['title']])
@if ($errors -> any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </div>
@endif
@php
    $url = ($config['method'] == 'create') ? route('post.store') : route("post.update", $post->id)
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-9">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>{{ __('messages.tableHeading') }}</h5>
                        </div>
                        <div class="ibox-content">
                            @include('backend.dashboard.component.content', ['model' => $post ?? null])
                        </div>
                    </div>
                    @include('backend.dashboard.component.seo', ['model' => $post ?? null] )
                </div>
                <div class="col-lg-3">
                    @include('backend.post.post.component.aside')
                </div>
            </div>
    
            @include('backend.dashboard.component.button')
        </div>
    </div>
</form>