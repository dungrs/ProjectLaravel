@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['create']['title']])
@include('backend.dashboard.component.formError')
@php
    $url = ($config['method'] == 'create') ? route('source.store') : route("source.update", $source->id)
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-9">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Thông tin nguồn khách</h5>
                    </div>
                    <div class="ibox-content sourceContent">
                        @include('backend.dashboard.component.content', ['offTitle' => true, 'offContent' => true, 'model' => ($source) ?? null])
                    </div>
                </div>
            </div>
            @include('backend.source.source.component.aside')
        </div>

        <div class="text-right mb15">
            <button class="btn btn-primary" type="submit" name="send" value="send">Lưu lại</button>
        </div>
    </div>
</form>

<script>
    const checkIconPath = "{{ asset('backend/img/check.png') }}";
    const deleteIconPath = "{{ asset('backend/img/close.png') }}"
</script>