@extends('layouts.blog')

@section('title', 'Latest Writings')
@section('meta_description', 'Latest essays on design, technology, and minimalism.')
@section('og_title', 'Latest Writings')
@section('og_description', 'Latest essays on design, technology, and minimalism.')

@section('content')
<div class="flex flex-col justify-center max-w-[960px] mx-auto w-full py-10 px-4 md:px-8 gap-10">
    <main class="flex flex-col gap-8">
        <div class="flex flex-col gap-6 mb-4">
            <div class="flex flex-col gap-2">
                <p class="text-5xl font-black leading-tight tracking-[-0.033em]">Latest Writings</p>
                <p class="text-[#617589] text-lg font-normal italic">Exploring the intersection of design, technology, and minimalism.</p>
            </div>
            <form class="w-full" action="{{ route('posts.index') }}" method="GET">
                <label class="flex flex-col h-12 w-full">
                    <div class="flex w-full flex-1 items-stretch rounded-xl h-full shadow-sm">
                        <div class="text-[#617589] flex border-none bg-white items-center justify-center pl-4 rounded-l-xl border-r-0">
                            <span class="material-symbols-outlined">search</span>
                        </div>
                        <input
                            class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden text-[#111418] focus:outline-0 focus:ring-0 border-none bg-white focus:border-none h-full placeholder:text-[#617589] px-4 rounded-r-xl border-l-0 pl-2 text-base font-normal leading-normal"
                            name="q"
                            placeholder="Search articles..."
                            value="{{ $search }}"
                        />
                    </div>
                </label>
            </form>
        </div>

        @if ($posts->count() === 0)
            <div class="rounded-xl bg-white p-8 text-center text-gray-500">
                Belum ada postingan yang dipublikasikan.
            </div>
        @else
            @php
                $featured = $posts->first();
                $restPosts = $posts->skip(1);
            @endphp

            @if ($featured)
                <article class="flex flex-col rounded-xl bg-white shadow-sm overflow-hidden group hover:shadow-md transition-shadow">
                    <a href="{{ route('posts.show', $featured) }}" class="block">
                        <div class="w-full h-64 bg-gray-200 relative overflow-hidden">
                            <div
                                class="w-full h-full bg-cover bg-center transform group-hover:scale-105 transition-transform duration-500"
                                style="background-image: url('{{ $featured->cover_image_path ? \Illuminate\Support\Facades\Storage::url($featured->cover_image_path) : 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?q=80&w=1400&auto=format&fit=crop' }}');"
                            ></div>
                        </div>
                        <div class="p-6 md:p-8 flex flex-col gap-4">
                            <div class="flex items-center gap-2 text-sm text-[#617589] font-sans">
                                <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                                <span>{{ optional($featured->published_at)->format('M d, Y') }}</span>
                                <span>•</span>
                                <span>{{ $featured->reading_time_text }}</span>
                                <span>•</span>
                                <span class="text-primary font-bold">Featured</span>
                            </div>
                            <h3 class="text-3xl font-bold text-[#111418] group-hover:text-primary transition-colors leading-tight">
                                {{ $featured->title }}
                            </h3>
                            <p class="text-[#617589] text-lg leading-relaxed">
                                {{ $featured->excerpt }}
                            </p>
                            <div class="pt-2 flex items-center gap-3">
                                <span class="text-sm font-bold text-[#111418] font-sans">{{ $featured->author?->name }}</span>
                                <span class="flex-1"></span>
                                <span class="text-primary font-bold text-sm flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                                    Read Article <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                </span>
                            </div>
                        </div>
                    </a>
                </article>
            @endif

            <div class="flex flex-col gap-6">
                @foreach ($restPosts as $post)
                    <article class="flex flex-col md:flex-row items-stretch gap-6 rounded-xl bg-white p-5 shadow-sm hover:shadow-md transition-shadow group">
                        <a href="{{ route('posts.show', $post) }}" class="flex flex-col md:flex-row items-stretch gap-6 w-full">
                            <div class="w-full md:w-48 h-48 md:h-auto shrink-0 rounded-lg overflow-hidden relative bg-gray-200">
                                <div
                                    class="w-full h-full bg-cover bg-center transform group-hover:scale-105 transition-transform duration-500"
                                    style="background-image: url('{{ $post->cover_image_path ? \Illuminate\Support\Facades\Storage::url($post->cover_image_path) : 'https://images.unsplash.com/photo-1517433456452-f9633a875f6f?q=80&w=900&auto=format&fit=crop' }}');"
                                ></div>
                            </div>
                            <div class="flex flex-col gap-3 justify-center flex-1">
                                <div class="flex items-center gap-2 text-xs font-sans text-[#617589] uppercase tracking-wider">
                                    <span>{{ optional($post->published_at)->format('M d, Y') }}</span>
                                    <span>•</span>
                                    <span>{{ $post->author?->name }}</span>
                                </div>
                                <h3 class="text-2xl font-bold text-[#111418] group-hover:text-primary transition-colors">
                                    {{ $post->title }}
                                </h3>
                                <p class="text-[#617589] text-base leading-relaxed">
                                    {{ $post->excerpt }}
                                </p>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $posts->links('vendor.pagination.blog') }}
            </div>
        @endif
    </main>
</div>
@endsection
