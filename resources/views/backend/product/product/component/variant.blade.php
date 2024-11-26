<div class="ibox variant-box">
    <div class="ibox-title">
        <h5 style="">Sản phẩm có nhiều phiên bản</h5>
        <div class="description">
            <p>Cho phéo bạn bán các phiên bản khác nhau của sản phẩm, ví dụ: quần,áo có các <strong class="text-danger">màu sắc </strong> và <strong class="text-danger">size</strong> số khác nhau. Mỗi phiên bản sẽ là 1 là dòng trong danh sách phiên bản phía dưới</p>
        </div>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="variant-checkbox uk-flex uk-flex-middle">
                    <input type="checkbox" value="1" name="accept" id="variantCheckbox" class="variantInputCheckBox" {{ (old('accept') == 1 || (isset($product) && count($product->product_variants) > 0)) ? 'checked' : ''}}>
                    <label for="variantCheckbox" class="turnOnVariant">Sản phẩm này có nhiều biến thể. Ví dụ như khác nhau về màu sắc, kích thước</label>
                </div>
            </div>  
        </div>
        @php
            $attributeCatalogueResponse = old('attributeCatalogue', (isset($product->attributeCatalogue) ? json_decode($product->attributeCatalogue, TRUE) : [] )) 
        @endphp
        <div class="variant-wrapper {{ (count($attributeCatalogueResponse) > 0) ? '' : 'hidden'  }}">
            <div class="row variant-container">
                <div class="col-lg-4">
                    <div class="attribute-title">Chọn thuộc tính</div>
                </div>
                <div class="col-lg-8">
                    <div class="attribute-title">Chọn giá trị của thuộc tính (nhập 2 từ để tìm kiếm)</div>
                </div>
            </div>
            <div class="variant-body">
                @if($attributeCatalogueResponse && count($attributeCatalogueResponse) > 0)
                @foreach ($attributeCatalogueResponse as $keyAttr => $valAttr)
                    <div class="row mb20 variant-item">
                        <div class="col-lg-4">
                            <div class="attribute-catalogue">
                                <select name="attributeCatalogue[]" class="choose-attribute niceSelect">
                                    <option value="">Chọn nhóm thuộc tính</option>
                                    @foreach ($attributeCatalogue as $item)
                                        <option {{ ($valAttr == $item->id ) ? 'selected' : '' }} value="{{ $item->id }}">{{ $item->attribute_catalogue_language->first()->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            {{-- <input type="text" name="attribute_value[]" disabled class="fake-variant form-control" placeholder="Nhập giá trị thuộc tính"> --}}
                            <select name="attribute[{{ $valAttr }}][]" class="selectVariant variant-{{ $valAttr }} form-control" multiple data-catid="{{ $valAttr }}" id=""></select>
                        </div>
                        <div class="col-lg-1">
                            <button type="button" class="remove-attribute btn btn-danger"><i class="fa fa-trash"></i></button>
                        </div>
                    </div>
                @endforeach
                @endif
            </div>
            <div class="variant-foot mt10">
                <button type="button" class="add-variant">Thêm phiên bản mới</button>
            </div>
        </div>
    </div>
</div>

<div class="ibox product-variant">
    <div class="ibox-title">
        <h5>Danh sách phiên bản</h5>
    </div>
    <div class="ibox-content">
        <table class="table table-stripped variantTable">
            <thead></thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script>
    @php
        $attribute = old('attribute', isset($product->attribute) ? json_decode($product->attribute, true) : []);
        $variant = old('variant', isset($product->variant) ? json_decode($product->variant, true) : []);
    @endphp
    var attribute = '{{ base64_encode(json_encode($attribute)) }}';
    var variant = '{{ base64_encode(json_encode($variant)) }}'

    // map là phương thức để lặp qua các phần tử trong eloquent collection
    var attributeCatalogue = @json($attributeCatalogue->map(function($item) {
        $name = $item->attribute_catalogue_language->first()->name;
        return [
            'id' => $item->id,
            'name' => $name
        ];
    })->values())

</script>