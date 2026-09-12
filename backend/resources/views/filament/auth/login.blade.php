@php
    $chapterName = \App\Models\SiteSetting::current()->short_name ?? \App\Models\SiteSetting::current()->chapter_name;
@endphp

<div style="display: flex; flex-wrap: wrap; min-height: calc(100vh - 4rem); margin: -2rem -1.5rem; overflow: hidden; border-radius: 0.5rem;">
    <div style="flex: 1 1 22rem; background-color: #000066; color: #ffffff; padding: 3rem 2.5rem; display: flex; flex-direction: column; justify-content: center; gap: 2rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <img src="{{ asset('images/logo.png') }}" alt="{{ $chapterName }}" style="height: 2.5rem; width: auto;" />
            <span style="font-size: 1.1rem; font-weight: 700; letter-spacing: 0.02em;">{{ $chapterName }}</span>
        </div>

        <div>
            <h1 style="font-size: 1.75rem; font-weight: 700; line-height: 1.25; margin: 0 0 0.75rem;">
                {{ $this->getHeading() }}
            </h1>
            <p style="font-size: 0.98rem; color: #cbd5ff; margin: 0; line-height: 1.5;">
                {{ $this->getSubheading() }}
            </p>
        </div>

        <ul style="list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 1rem;">
            @foreach ($this->getHighlights() as $highlight)
                <li style="display: flex; align-items: flex-start; gap: 0.75rem; font-size: 0.92rem; line-height: 1.5; color: #e6e9ff;">
                    <span style="flex-shrink: 0; width: 1.5rem; height: 1.5rem; border-radius: 9999px; background-color: #ffff00; color: #000066; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem;">
                        &#10003;
                    </span>
                    <span>{{ $highlight }}</span>
                </li>
            @endforeach
        </ul>

        <p style="margin: 0; font-size: 0.78rem; color: #9aa4e0;">
            &copy; {{ now()->year }} {{ $chapterName }}. All rights reserved.
        </p>
    </div>

    <div style="flex: 1 1 20rem; background-color: var(--fi-color-white, #ffffff); padding: 3rem 2.5rem; display: flex; align-items: center; justify-content: center;">
        <div style="width: 100%; max-width: 24rem;">
            {{ $this->content }}
        </div>
    </div>
</div>
