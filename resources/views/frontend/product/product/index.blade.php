@extends('frontend.homepage.layout')
@section('content')
    <div class="product-catalogue page-wrapper">
        <div class="uk-container uk-container-center">
            @include('frontend.component.breadcrumb', ['model' => $productCatalogue, 'breadcrumb' => $breadcrumb])
            
        </div>
    </div>

    <div class="product-container">
        <div class="uk-container uk-container-center">
            <div class="panel-head">
                @include('frontend.component.product-details', ['product' => $product, 'productCatalogue' => $productCatalogue])
            </div>
        </div>
    </div>
@endsection