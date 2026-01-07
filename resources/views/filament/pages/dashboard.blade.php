<x-filament::page>
    <div class="flex flex-col gap-8">
        <header class="flex flex-wrap justify-between items-end gap-6">
            <div class="flex flex-col gap-2">
                <h1 class="text-3xl md:text-4xl font-black tracking-tight text-slate-900 dark:text-white">
                    Dashboard
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-base">
                    Overview of your blog's performance and recent activity.
                </p>
            </div>
            <a
                href="{{ route('filament.admin.resources.posts.create') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-3 text-sm font-bold text-white shadow-sm shadow-blue-500/20 hover:bg-primary/90 transition"
            >
                <x-heroicon-o-pencil-square class="h-5 w-5" />
                Create New Post
            </a>
        </header>

        <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="flex flex-col gap-4 rounded-xl p-6 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm">
                <div class="flex justify-between items-start">
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-primary">
                        <x-heroicon-o-document-text class="h-5 w-5" />
                    </div>
                    <span class="text-green-600 bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded text-xs font-bold">
                        {{ $publishedPosts }} published
                    </span>
                </div>
                <div>
                    <p class="text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wider">
                        Total Posts
                    </p>
                    <p class="text-slate-900 dark:text-white text-3xl font-black mt-1">{{ $totalPosts }}</p>
                </div>
            </div>

            <div class="flex flex-col gap-4 rounded-xl p-6 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 w-16 h-16 bg-orange-500/10 rounded-bl-full -mr-2 -mt-2"></div>
                <div class="flex justify-between items-start">
                    <div class="p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg text-orange-600">
                        <x-heroicon-o-chat-bubble-left-right class="h-5 w-5" />
                    </div>
                    <span class="text-orange-600 bg-orange-50 dark:bg-orange-900/20 px-2 py-1 rounded text-xs font-bold">
                        Action Required
                    </span>
                </div>
                <div>
                    <p class="text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wider">
                        Pending Comments
                    </p>
                    <p class="text-slate-900 dark:text-white text-3xl font-black mt-1">{{ $pendingComments }}</p>
                </div>
            </div>

            <div class="flex flex-col gap-4 rounded-xl p-6 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm">
                <div class="flex justify-between items-start">
                    <div class="p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg text-purple-600">
                        <x-heroicon-o-chart-bar-square class="h-5 w-5" />
                    </div>
                    <span class="text-green-600 bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded text-xs font-bold">
                        Active
                    </span>
                </div>
                <div>
                    <p class="text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wider">
                        Total Comments
                    </p>
                    <p class="text-slate-900 dark:text-white text-3xl font-black mt-1">{{ $totalComments }}</p>
                </div>
            </div>
        </section>

        <section class="flex flex-col gap-4">
            <div class="flex items-center justify-between px-1">
                <h2 class="text-slate-900 dark:text-white text-2xl font-bold tracking-tight">Recent Activity</h2>
                <a class="text-primary text-sm font-bold hover:underline" href="{{ route('filament.admin.resources.comments.index') }}">
                    View All History
                </a>
            </div>

            <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shadow-sm overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                            <th class="px-6 py-4 text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wider w-1/2">
                                Action
                            </th>
                            <th class="px-6 py-4 text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wider">
                                User
                            </th>
                            <th class="px-6 py-4 text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wider">
                                Date
                            </th>
                            <th class="px-6 py-4 text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wider text-right">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse ($recentComments as $comment)
                            <tr class="group hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-blue-100 dark:bg-blue-900/30 text-primary p-2 rounded-full">
                                            <x-heroicon-o-chat-bubble-left-right class="h-4 w-4" />
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-slate-900 dark:text-white font-bold text-sm">
                                                New comment on '{{ $comment->post?->title ?? 'Post' }}'
                                            </span>
                                            <span class="text-slate-500 dark:text-slate-400 text-xs">
                                                "{{ \Illuminate\Support\Str::limit($comment->body_sanitized, 48) }}"
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-600 dark:text-slate-300 text-sm font-sans">
                                    {{ $comment->author_name }}
                                </td>
                                <td class="px-6 py-4 text-slate-500 dark:text-slate-400 text-sm font-sans">
                                    {{ $comment->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @php
                                        $statusClass = match($comment->status->value) {
                                            'approved' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                            'spam' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                            'deleted' => 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-300',
                                            default => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }} font-sans">
                                        {{ ucfirst($comment->status->value) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-6 text-center text-sm text-slate-500">
                                    No recent activity yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-filament::page>
