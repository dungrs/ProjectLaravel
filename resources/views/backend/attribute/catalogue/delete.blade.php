@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['create']['title']])
@include('backend.dashboard.component.formError')
<form action="{{ route('attribute.catalogue.destroy', $attributeCatalogue->id) }}" method="post" class="box">
    @include('backend.dashboard.component.destroy', ['model' => ($attributeCatalogue)] ?? null)
</form>