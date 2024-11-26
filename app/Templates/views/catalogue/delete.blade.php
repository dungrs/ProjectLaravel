@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['create']['title']])
@include('backend.dashboard.component.formError')
<form action="{{ route('{view}.destroy', ${module}->id) }}" method="post" class="box">
    @include('backend.dashboard.component.destroy', ['model' => (${module})] ?? null)
</form>