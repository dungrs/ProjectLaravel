@foreach ($languages as $language)
    @if ($language->canonical === session('app_locale'))
        @continue
    @endif

    @php
        // Kiểm tra xem ngôn ngữ này có tồn tại trong $model->languages (nghĩa là đã dịch)
        $translated = $model->languages->firstWhere('canonical', $language->canonical);
    @endphp

    <td class="text-center">
        <a href="{{ route('language.translate', ['id' => $model->id, 'languageId' => $language->id, 'model' => $modeling]) }}" style="color: {{ ($translated) ? 'red' : '' }}"> {{ ($translated) ?  'Đã dịch' : 'Chưa dịch' }}</a>
    </td>
@endforeach
