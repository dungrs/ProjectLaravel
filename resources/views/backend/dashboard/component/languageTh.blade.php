@foreach ($languages as $language)
    @if ($language->canonical === session('app_locale'))
        @continue
    @endif
    <th class="text-center">
        <span class="image img-scaledown language-flag">
            <img src="{{ $language->image }}" alt="">
        </span>
    </th>
@endforeach