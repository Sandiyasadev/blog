@extends('layouts.blog')

@section('title', 'Contact')
@section('meta_description', 'Get in touch with us.')

@section('content')
<div class="flex flex-col items-center w-full">
    <main class="w-full max-w-[600px] flex flex-col px-4 sm:px-6 py-8 sm:py-12">
        <h1 class="text-4xl md:text-5xl font-black leading-tight tracking-[-0.02em] mb-4 text-gray-900">
            Contact
        </h1>
        <p class="text-gray-500 text-lg mb-8">
            Ada pertanyaan atau ingin berdiskusi? Kirim pesan melalui form di bawah ini.
        </p>

        @if (session('success'))
            <div class="mb-6 rounded-lg bg-green-50 text-green-700 px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('contact.send') }}" class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            @csrf
            <div class="flex flex-col gap-5">
                <div>
                    <label class="block text-sm font-medium mb-2" for="name">Nama</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        class="w-full rounded-lg border-gray-200 focus:border-primary focus:ring-primary"
                        required
                    />
                    @error('name')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="w-full rounded-lg border-gray-200 focus:border-primary focus:ring-primary"
                        required
                    />
                    @error('email')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" for="subject">Subjek</label>
                    <input
                        type="text"
                        id="subject"
                        name="subject"
                        value="{{ old('subject') }}"
                        class="w-full rounded-lg border-gray-200 focus:border-primary focus:ring-primary"
                        required
                    />
                    @error('subject')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2" for="message">Pesan</label>
                    <textarea
                        id="message"
                        name="message"
                        rows="5"
                        class="w-full rounded-lg border-gray-200 focus:border-primary focus:ring-primary"
                        required
                    >{{ old('message') }}</textarea>
                    @error('message')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Honeypot --}}
                <div class="hidden">
                    <label for="website">Website</label>
                    <input type="text" id="website" name="website" value="" />
                </div>

                @if (config('services.turnstile.site_key'))
                    <div>
                        <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}" data-theme="light"></div>
                        @error('cf-turnstile-response')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <div>
                    <button type="submit" class="w-full rounded-lg bg-primary text-white font-bold py-3 hover:bg-blue-600 transition-colors">
                        Kirim Pesan
                    </button>
                </div>
            </div>
        </form>
    </main>
</div>
@endsection
