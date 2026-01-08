<!doctype html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="robots" content="@yield('robots', 'index, follow')" />

    {{-- Basic Meta --}}
    <title>@yield('title', 'Blog') | {{ config('app.name', 'The Daily Journal') }}</title>
    <meta name="description" content="@yield('meta_description', 'Personal blog about design, technology, and minimal living.')" />
    <meta name="author" content="@yield('author', config('app.name'))" />

    {{-- Canonical URL --}}
    <link rel="canonical" href="@yield('canonical', url()->current())" />

    {{-- Open Graph --}}
    <meta property="og:site_name" content="{{ config('app.name', 'The Daily Journal') }}" />
    <meta property="og:title" content="@yield('og_title', trim($__env->yieldContent('title', 'Blog')))" />
    <meta property="og:description" content="@yield('og_description', trim($__env->yieldContent('meta_description', 'Personal blog about design, technology, and minimal living.')))" />
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.jpg'))" />
    <meta property="og:type" content="@yield('og_type', 'website')" />
    <meta property="og:url" content="@yield('canonical', url()->current())" />
    <meta property="og:locale" content="id_ID" />

    {{-- Twitter Cards --}}
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="@yield('og_title', trim($__env->yieldContent('title', 'Blog')))" />
    <meta name="twitter:description" content="@yield('og_description', trim($__env->yieldContent('meta_description', 'Personal blog about design, technology, and minimal living.')))" />
    <meta name="twitter:image" content="@yield('og_image', asset('images/og-default.jpg'))" />
    @hasSection('twitter_creator')
        <meta name="twitter:creator" content="@yield('twitter_creator')" />
    @endif

    {{-- Article specific meta (for blog posts) --}}
    @hasSection('published_time')
        <meta property="article:published_time" content="@yield('published_time')" />
    @endif
    @hasSection('modified_time')
        <meta property="article:modified_time" content="@yield('modified_time')" />
    @endif


    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Additional Head Content --}}
    @stack('head')
</head>
<body class="bg-background-light text-[#111418] font-display">
<div class="relative flex min-h-screen w-full flex-col overflow-x-hidden" x-data="{ mobileMenuOpen: false }">
    {{-- Header --}}
    <header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-gray-200 bg-white px-4 md:px-6 py-4 sticky top-0 z-50">
        <div class="flex items-center gap-3 md:gap-4">
            <div class="size-8 flex items-center justify-center text-primary">
                <span class="material-symbols-outlined text-2xl md:text-3xl">edit_note</span>
            </div>
            <a href="{{ route('home') }}" class="text-xl md:text-2xl font-bold leading-tight tracking-[-0.015em]">
                The Daily Journal
            </a>
        </div>
        <div class="flex flex-1 justify-end gap-4 md:gap-8 items-center">
            {{-- Desktop Navigation --}}
            <nav class="hidden md:flex items-center gap-9 text-base font-medium">
                <a class="hover:text-primary transition-colors" href="{{ route('home') }}">Home</a>
                <a class="hover:text-primary transition-colors" href="{{ route('about') }}">About</a>
                <a class="hover:text-primary transition-colors" href="{{ route('posts.index') }}">Writings</a>
            </nav>
            {{-- Mobile Menu Button --}}
            <button
                @click="mobileMenuOpen = !mobileMenuOpen"
                class="md:hidden flex items-center justify-center p-2 rounded-lg hover:bg-gray-100 transition-colors"
                :aria-expanded="mobileMenuOpen"
                aria-label="Toggle menu"
            >
                <span class="material-symbols-outlined text-2xl" x-show="!mobileMenuOpen">menu</span>
                <span class="material-symbols-outlined text-2xl" x-show="mobileMenuOpen" x-cloak>close</span>
            </button>
        </div>
    </header>

    {{-- Mobile Navigation Overlay --}}
    <div
        x-show="mobileMenuOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="md:hidden fixed inset-0 z-40 bg-black/50"
        @click="mobileMenuOpen = false"
        x-cloak
    ></div>

    {{-- Mobile Navigation Menu --}}
    <nav
        x-show="mobileMenuOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="md:hidden fixed top-0 right-0 z-50 h-full w-64 bg-white shadow-xl flex flex-col"
        x-cloak
    >
        <div class="flex items-center justify-between p-4 border-b border-gray-100">
            <span class="font-bold text-lg">Menu</span>
            <button @click="mobileMenuOpen = false" class="p-2 rounded-lg hover:bg-gray-100">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="flex flex-col p-4 gap-1">
            <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 font-medium" @click="mobileMenuOpen = false">
                <span class="material-symbols-outlined text-xl">home</span>
                Home
            </a>
            <a href="{{ route('about') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 font-medium" @click="mobileMenuOpen = false">
                <span class="material-symbols-outlined text-xl">person</span>
                About
            </a>
            <a href="{{ route('posts.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 font-medium" @click="mobileMenuOpen = false">
                <span class="material-symbols-outlined text-xl">article</span>
                Writings
            </a>
        </div>
    </nav>

    @yield('content')

    {{-- Footer --}}
    <footer class="mt-16 border-t border-gray-200 bg-white">
        <div class="max-w-[1200px] mx-auto px-4 md:px-6 py-8 text-sm text-gray-500 flex flex-col md:flex-row gap-4 md:items-center md:justify-between">
            <span>&copy; {{ date('Y') }} The Daily Journal. All rights reserved.</span>
            <div class="flex gap-4">
                <a href="{{ route('about') }}" class="hover:text-primary">About</a>
            </div>
        </div>
    </footer>

    {{-- Scroll to Top Button --}}
    <button
        x-data="{ showScrollTop: false }"
        x-init="window.addEventListener('scroll', () => { showScrollTop = window.scrollY > 500 })"
        x-show="showScrollTop"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
        class="fixed bottom-6 right-6 z-40 p-3 rounded-full bg-primary text-white shadow-lg hover:bg-blue-600 transition-colors"
        aria-label="Scroll to top"
        x-cloak
    >
        <span class="material-symbols-outlined">arrow_upward</span>
    </button>
</div>
</body>
</html>
