@php
    $swanSettings = \App\Models\SiteSetting::current();
    $swanSiteUrl = rtrim(config('app.frontend_url'), '/');
@endphp
<x-mail::layout>
{{-- Header --}}
<x-slot:header>
<x-mail::header :url="$swanSiteUrl">
{{ $swanSettings->short_name ?? $swanSettings->chapter_name }}
</x-mail::header>
</x-slot:header>

{{-- Body --}}
{!! $slot !!}

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{!! $subcopy !!}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
© {{ date('Y') }} {{ $swanSettings->chapter_name }}. {{ __('All rights reserved.') }}<br>
<a href="{{ $swanSiteUrl }}">{{ str_replace(['https://', 'http://'], '', $swanSiteUrl) }}</a>
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
