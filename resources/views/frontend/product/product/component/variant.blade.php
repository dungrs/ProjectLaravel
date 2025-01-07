@if (!is_null($attributeCatalogue))
    @foreach ($attributeCatalogue as $key => $val)
        <div class="attribute">
            <div class="attribute-item attribute-color">
                <div class="label">{{ $val->name }}: <span></span></div>
                @if (!is_null($val->attributes))
                    <div class="attribute-value">
                        @foreach ($val->attributes as $attr)
                            <a class="choose-attribute" data-attributeid="{{ $attr->id }}" title="{{ $attr->name }}">{{ $attr->name }}</a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div><!-- .attribute -->
    @endforeach
    <input type="hidden" name="product_id" value="{{ $product->id }}">
    <input type="hidden" name="language_id" value="{{ $languageId }}">
@endif