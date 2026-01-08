@extends('layouts.blog')

@section('title', $post->title)
@section('meta_description', $post->excerpt ?? 'Blog post')
@section('og_title', $post->title)
@section('og_description', $post->excerpt ?? 'Blog post')
@section('og_type', 'article')
@section('og_image', $post->cover_image_path ? \Illuminate\Support\Facades\Storage::url($post->cover_image_path) : asset('images/og-default.jpg'))
@section('canonical', route('posts.show', $post->slug))
@section('author', $post->author?->name)
@section('published_time', $post->published_at?->toIso8601String())
@section('modified_time', $post->updated_at?->toIso8601String())
@if ($post->author?->twitter_handle)
    @section('twitter_creator', '@' . $post->author->twitter_handle)
@endif

@section('content')
<div class="flex flex-col items-center w-full">
    <main class="w-full max-w-[960px] flex flex-col px-4 sm:px-6 py-8 sm:py-12 bg-surface-light shadow-sm my-0 sm:my-8 sm:rounded-xl">
        <nav class="flex flex-wrap gap-2 mb-6 text-sm">
            <a class="text-gray-500 hover:text-primary font-medium" href="{{ route('home') }}">Home</a>
            <span class="text-gray-400">/</span>
            <a class="text-gray-500 hover:text-primary font-medium" href="{{ route('posts.index') }}">Blog</a>
            <span class="text-gray-400">/</span>
            <span class="text-current font-medium">{{ $post->title }}</span>
        </nav>

        <div class="mb-8">
            <h1 class="text-4xl md:text-5xl font-black leading-tight tracking-[-0.02em] mb-4 text-gray-900">
                {{ $post->title }}
            </h1>
            <p class="text-xl text-gray-500 font-light leading-relaxed max-w-2xl">
                {{ $post->excerpt }}
            </p>
        </div>

        <div class="flex items-center gap-4 mb-8 py-4 border-y border-gray-100">
            <div class="h-12 w-12 rounded-full bg-gray-200 overflow-hidden">
                <img class="h-full w-full object-cover" alt="{{ $post->author?->name }}" src="{{ $post->author?->avatar_url ?? 'https://www.gravatar.com/avatar/?d=mp&s=200' }}" />
            </div>
            <div class="flex flex-col justify-center">
                <p class="text-base font-bold leading-none mb-1">{{ $post->author?->name }}</p>
                <p class="text-gray-500 text-sm font-normal">{{ optional($post->published_at)->format('M d, Y') }} • {{ $post->reading_time_text }}</p>
            </div>
            <div class="ml-auto flex gap-2" x-data="{ copied: false, showShareMenu: false }">
                {{-- Share Button --}}
                <div class="relative">
                    <button
                        @click="showShareMenu = !showShareMenu"
                        class="p-2 rounded-full hover:bg-gray-100 transition-colors text-gray-500"
                        title="Share"
                    >
                        <span class="material-symbols-outlined">share</span>
                    </button>
                    {{-- Share Dropdown --}}
                    <div
                        x-show="showShareMenu"
                        @click.outside="showShareMenu = false"
                        x-transition
                        class="absolute right-0 top-full mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-10"
                        x-cloak
                    >
                        <a
                            href="https://twitter.com/intent/tweet?url={{ urlencode(route('posts.show', $post->slug)) }}&text={{ urlencode($post->title) }}"
                            target="_blank"
                            class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 text-sm"
                        >
                            <span class="material-symbols-outlined text-lg">share</span>
                            Twitter
                        </a>
                        <a
                            href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('posts.show', $post->slug)) }}"
                            target="_blank"
                            class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 text-sm"
                        >
                            <span class="material-symbols-outlined text-lg">thumb_up</span>
                            Facebook
                        </a>
                        <a
                            href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(route('posts.show', $post->slug)) }}&title={{ urlencode($post->title) }}"
                            target="_blank"
                            class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 text-sm"
                        >
                            <span class="material-symbols-outlined text-lg">work</span>
                            LinkedIn
                        </a>
                        <hr class="my-1 border-gray-100" />
                        <button
                            @click="navigator.clipboard.writeText('{{ route('posts.show', $post->slug) }}'); copied = true; showShareMenu = false; setTimeout(() => copied = false, 2000)"
                            class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 text-sm w-full"
                        >
                            <span class="material-symbols-outlined text-lg">link</span>
                            Copy Link
                        </button>
                    </div>
                </div>
                {{-- Copy Link Feedback --}}
                <div
                    x-show="copied"
                    x-transition
                    class="fixed bottom-20 right-6 bg-gray-900 text-white px-4 py-2 rounded-lg text-sm shadow-lg z-50"
                    x-cloak
                >
                    Link copied!
                </div>
            </div>
        </div>

        <div class="w-full aspect-[21/9] rounded-xl overflow-hidden mb-12 bg-gray-200">
            <img
                alt="{{ $post->title }}"
                class="w-full h-full object-cover"
                src="{{ $post->cover_image_path ? \Illuminate\Support\Facades\Storage::url($post->cover_image_path) : 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?q=80&w=1400&auto=format&fit=crop' }}"
            />
        </div>

        <article class="article-body max-w-[720px] mx-auto w-full font-serif">
            {!! $bodyHtml !!}
        </article>

        <hr class="my-12 border-gray-200 w-full" />

        @if ($post->author)
            <div class="max-w-[720px] mx-auto w-full bg-background-light p-6 rounded-xl flex flex-col sm:flex-row gap-6 items-start sm:items-center">
                <div class="bg-center bg-no-repeat aspect-square bg-cover rounded-full h-20 w-20 shrink-0" style="background-image: url('{{ $post->author->avatar_url }}');"></div>
                <div>
                    <p class="text-lg font-bold">{{ $post->author->name }}</p>
                    <p class="text-sm text-gray-600">
                        {{ $post->author->display_bio }}
                    </p>
                </div>
            </div>
        @endif

        {{-- Related Posts --}}
        @if ($relatedPosts->isNotEmpty())
            <section class="max-w-[720px] mx-auto w-full mt-12">
                <h2 class="text-2xl font-bold mb-6">Artikel Terkait</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($relatedPosts as $related)
                        <a href="{{ route('posts.show', $related) }}" class="group block bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                            <div class="h-32 bg-gray-200 overflow-hidden">
                                <div
                                    class="w-full h-full bg-cover bg-center transform group-hover:scale-105 transition-transform duration-300"
                                    style="background-image: url('{{ $related->cover_image_path ? \Illuminate\Support\Facades\Storage::url($related->cover_image_path) : 'https://images.unsplash.com/photo-1517433456452-f9633a875f6f?q=80&w=400&auto=format&fit=crop' }}');"
                                ></div>
                            </div>
                            <div class="p-4">
                                <p class="text-xs text-gray-500 mb-1">{{ optional($related->published_at)->format('M d, Y') }} • {{ $related->reading_time_text }}</p>
                                <h3 class="font-bold text-sm group-hover:text-primary transition-colors line-clamp-2">{{ $related->title }}</h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

    </main>
</div>
@endsection
