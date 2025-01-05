<div class="category-children">
    <ul class="uk-list uk-clearfix uk-flex uk-flex-middle">
        @php
            $count = 0;
        @endphp
        @foreach ($category as $item)
            @if ($count >= 7)
                @break
            @endif

            @if (is_object($item) && isset($item->name))
                <li class="">
                    <a href="{{ writeUrl($item->canonical, true, true) }}" title="">
                        {{ \Illuminate\Support\Str::limit($item->name, 20) }}
                    </a>
                </li>
                @php
                    $count++;
                @endphp
            @endif
        @endforeach
    </ul>
</div>
