@props(['cat_class', 'cat_name'])
<details class="day_form_info">
    <summary class="day_form_info_btn"><img src="{{ route('image.show', 'noun-5444978-FFFFFF.svg') }}" alt="{{__('(Infos zu ') . $cat_name . __(')')}}"></summary>
    <div class="day_form_info_content {{ $cat_class }}">
        {{ $slot }}
    </div>
</details>
