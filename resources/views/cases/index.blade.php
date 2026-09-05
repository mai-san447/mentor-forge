<x-layouts::app :title="__('ケースドリル')">
    <div class="space-y-6">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-teal-600">CASE DRILL</p>
            <h1 class="mt-2 text-3xl font-bold">ケースドリル</h1>
            <p class="mt-2 max-w-2xl text-zinc-600">ケースを読み、あなたならどう返すかを考えます。ほかの人の回答は、自分の回答を投稿した後に匿名で開示されます。</p>
        </div>

        <div class="grid gap-5 md:grid-cols-2">
            @forelse($scenarios as $scenario)
                <a href="{{ route('cases.show', $scenario) }}" class="rounded-2xl border border-zinc-200 bg-white p-6 transition hover:-translate-y-0.5 hover:border-teal-300 hover:shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="text-sm font-semibold text-teal-700">{{ $scenario->persona->role }} / {{ $scenario->difficulty }}</p>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $scenario->answered ? 'bg-teal-100 text-teal-800' : 'bg-zinc-100 text-zinc-600' }}">
                            {{ $scenario->answered ? '回答済み' : '未回答' }}
                        </span>
                    </div>
                    <h2 class="mt-3 text-xl font-bold text-zinc-900">{{ $scenario->title }}</h2>
                    <p class="mt-3 text-sm leading-6 text-zinc-600">{{ $scenario->situation }}</p>
                    <span class="mt-5 inline-block text-sm font-semibold text-teal-700">ケースを見る →</span>
                </a>
            @empty
                <div class="rounded-2xl border border-zinc-200 bg-white p-6 text-zinc-600">ケースは準備中です。</div>
            @endforelse
        </div>
    </div>
</x-layouts::app>
