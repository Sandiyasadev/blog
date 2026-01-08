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

        <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                        Published Posts
                    </p>
                    <p class="text-slate-900 dark:text-white text-3xl font-black mt-1">{{ $publishedPosts }}</p>
                </div>
            </div>
        </section>
    </div>
</x-filament::page>
