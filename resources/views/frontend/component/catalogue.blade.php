<div class="category-children">
    <ul class="uk-list uk-clearfix uk-flex uk-flex-middle">
        @foreach ($category as $item)
            @if (is_object($item) && isset($item->name))
                <li class=""><a href="{{ writeUrl($item->canonical) }}" title="">{{  \Illuminate\Support\Str::limit($item->name, 20) }}</a></li>
            @endif
        @endforeach
    </ul>
</div>
