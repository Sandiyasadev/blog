<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:content="http://purl.org/rss/1.0/modules/content/">
    <channel>
        <title>{{ config('app.name', 'Blog') }}</title>
        <link>{{ url('/') }}</link>
        <description>Latest posts from our blog</description>
        <language>id</language>
        <lastBuildDate>{{ now()->toRfc2822String() }}</lastBuildDate>
        <atom:link href="{{ route('feed.rss') }}" rel="self" type="application/rss+xml"/>

        @foreach ($posts as $post)
        <item>
            <title><![CDATA[{{ $post->title }}]]></title>
            <link>{{ route('posts.show', $post->slug) }}</link>
            <guid isPermaLink="true">{{ route('posts.show', $post->slug) }}</guid>
            <pubDate>{{ $post->published_at->toRfc2822String() }}</pubDate>
            @if ($post->author)
            <author>{{ $post->author->email }} ({{ $post->author->name }})</author>
            @endif
            <description><![CDATA[{{ $post->excerpt }}]]></description>
            <content:encoded><![CDATA[{!! \Illuminate\Support\Str::markdown($post->body ?? '', ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}]]></content:encoded>
        </item>
        @endforeach
    </channel>
</rss>
