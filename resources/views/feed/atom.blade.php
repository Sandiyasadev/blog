<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<feed xmlns="http://www.w3.org/2005/Atom">
    <title>{{ config('app.name', 'Blog') }}</title>
    <subtitle>Latest posts from our blog</subtitle>
    <link href="{{ route('feed.atom') }}" rel="self"/>
    <link href="{{ url('/') }}"/>
    <id>{{ url('/') }}</id>
    <updated>{{ now()->toAtomString() }}</updated>

    @foreach ($posts as $post)
    <entry>
        <title><![CDATA[{{ $post->title }}]]></title>
        <link href="{{ route('posts.show', $post->slug) }}"/>
        <id>{{ route('posts.show', $post->slug) }}</id>
        <published>{{ $post->published_at->toAtomString() }}</published>
        <updated>{{ $post->updated_at->toAtomString() }}</updated>
        @if ($post->author)
        <author>
            <name>{{ $post->author->name }}</name>
            <email>{{ $post->author->email }}</email>
        </author>
        @endif
        <summary type="html"><![CDATA[{{ $post->excerpt }}]]></summary>
        <content type="html"><![CDATA[{!! \Illuminate\Support\Str::markdown($post->body ?? '', ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}]]></content>
    </entry>
    @endforeach
</feed>
