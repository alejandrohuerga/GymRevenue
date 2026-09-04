@props(['eyebrow' => '', 'title' => '', 'lead' => ''])

<section class="max-w-6xl mx-auto px-6 pt-16 md:pt-20 pb-12 border-b border-zinc-800">
    <div class="max-w-3xl">
        @if ($eyebrow)
            <p class="text-sm uppercase tracking-widest text-emerald-400 mb-4">{{ $eyebrow }}</p>
        @endif

        <h1 class="text-4xl sm:text-5xl font-black tracking-tight leading-[1.05] text-balance">{{ $title }}</h1>

        @if ($lead)
            <p class="mt-6 text-zinc-400 leading-relaxed text-pretty">{{ $lead }}</p>
        @endif

        @isset($slot)
            @if ($slot->isNotEmpty())
                <div class="mt-8">{{ $slot }}</div>
            @endif
        @endisset
    </div>
</section>