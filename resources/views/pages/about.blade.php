@extends('layouts.blog')

@section('title', 'About')
@section('meta_description', 'Learn more about the author and this blog.')

@section('content')
<div class="flex flex-col items-center w-full">
    <main class="w-full max-w-[800px] flex flex-col px-4 sm:px-6 py-8 sm:py-12">
        <h1 class="text-4xl md:text-5xl font-black leading-tight tracking-[-0.02em] mb-8 text-gray-900">
            About
        </h1>

        @if ($author)
            <div class="flex flex-col md:flex-row gap-8 mb-12">
                <div class="shrink-0">
                    <div class="w-32 h-32 md:w-40 md:h-40 rounded-full overflow-hidden bg-gray-200">
                        <img
                            src="{{ $author->avatar_url }}"
                            alt="{{ $author->name }}"
                            class="w-full h-full object-cover"
                        />
                    </div>
                </div>
                <div class="flex-1">
                    <h2 class="text-2xl font-bold mb-2">{{ $author->name }}</h2>
                    <p class="text-gray-500 mb-4">{{ $author->display_title }}</p>
                    <p class="text-gray-700 leading-relaxed">
                        {{ $author->display_bio }}
                    </p>
                    <div class="flex items-center gap-4 mt-6">
                        @if ($author->website_url)
                            <a href="{{ $author->website_url }}" target="_blank" rel="noopener" class="flex items-center gap-2 text-primary hover:underline">
                                <span class="material-symbols-outlined text-xl">language</span>
                                <span>Website</span>
                            </a>
                        @endif
                        @if ($author->twitter_handle)
                            <a href="https://twitter.com/{{ $author->twitter_handle }}" target="_blank" rel="noopener" class="flex items-center gap-2 text-primary hover:underline">
                                <span class="material-symbols-outlined text-xl">share</span>
                                <span>Twitter</span>
                            </a>
                        @endif
                        <a href="mailto:{{ $author->email }}" class="flex items-center gap-2 text-primary hover:underline">
                            <span class="material-symbols-outlined text-xl">mail</span>
                            <span>Email</span>
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <div class="prose prose-lg max-w-none">
            <h2>Tentang Blog Ini</h2>
            <p>
                Blog ini adalah tempat saya berbagi pemikiran, pengalaman, dan pembelajaran tentang berbagai topik yang menarik bagi saya.
                Dari teknologi dan desain, hingga produktivitas dan gaya hidup minimalis.
            </p>

            <h3>Apa yang Akan Anda Temukan</h3>
            <ul>
                <li><strong>Artikel Mendalam</strong> - Eksplorasi topik-topik yang menarik dengan pendekatan yang thoughtful.</li>
                <li><strong>Tutorial Praktis</strong> - Panduan langkah-demi-langkah yang bisa langsung dipraktikkan.</li>
                <li><strong>Refleksi Pribadi</strong> - Pemikiran dan pembelajaran dari pengalaman sehari-hari.</li>
            </ul>

            <h3>Kontak</h3>
            <p>
                Punya pertanyaan atau ingin berdiskusi? Jangan ragu untuk <a href="{{ route('contact') }}">menghubungi saya</a>.
                Saya selalu senang mendengar dari pembaca.
            </p>
        </div>
    </main>
</div>
@endsection
