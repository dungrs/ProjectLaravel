@php
    $name = $model->name    
@endphp
<div class="page-breadcrumb background">
    <h1 class="heading-2"><span>{{ $name }}</span></h1>
    <ul class="uk-list uk-clearfix">
        <li><a href=""><i class="fi-rs-home mr5"></i>{{ __('frontend.home') }}</a></li>
        @if(!is_null($breadcrumb))
            @foreach ($breadcrumb as $item)
                @php
                    $name = $item->name;
                    $canonical = writeUrl($item->canonical, true, true)
                @endphp
                <li><a href="{{ $canonical }}" title="{{ $name }}">{{ $name }}</a></li>
            @endforeach
        @endif
    </ul>
</div>