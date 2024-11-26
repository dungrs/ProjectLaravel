@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['index']['title']])
<div class="row mt20">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title uk-flex uk-flex-middle uk-flex-space-between">
                <h5>{{ $config['seo']['index']['table'] }}</h5>
                @include('backend.dashboard.component.toolbox', ['model' => 'ProductCatalogue'])
            </div>
            <div class="ibox-content">
                @include('backend.product.catalogue.component.filter')
                @include('backend.product.catalogue.component.table')
            </div>
        </div>
    </div>
</div>