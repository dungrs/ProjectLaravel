@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['create']['title']])
@include('backend.dashboard.component.formError')
@php
    $url = ($config['method'] == 'create') ? route('promotion.store') : route("promotion.update", $promotion->id)
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight promotion-wrapper">
        <div class="row">
            <div class="col-lg-8">
                @include('backend.promotion.component.general', ['model' => ($promotion) ?? null])
                @include('backend.promotion.promotion.component.details', ['model' => ($promotion) ?? null])
            </div>
            @include('backend.promotion.component.aside', ['model' => ($promotion) ?? null])
        </div>

        <div class="text-right mb15">
            <button class="btn btn-primary" type="submit" name="send" value="send">Lưu lại</button>
        </div>
    </div>
</form>
<input type="hidden" class="preload_select_module_type" value="{{ old('module_type', $promotion->module_type ?? '') }}">
<input type="hidden" class="input_order_amount_range" value='@json(old('promotion_order_amount_range', []))'>
<input type="hidden" class="input_product_and_quantity" value='@json(old('product_and_quantity', []))'>\
<input type="hidden" class="input_object" value='@json(old('object', []))'>
@include('backend.promotion.promotion.component.popup')
<div id="productData" data-products='@json(__('module.item'))'></div>
