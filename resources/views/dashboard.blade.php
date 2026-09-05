<x-layouts::app :title="__('Mentor Forge')">
    <div class="space-y-8">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-teal-600">MENTOR FORGE</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight">学んで、試して、実践する</h1>
            <p class="mt-2 max-w-2xl text-zinc-600">メンタリングの知識をクイズで学び、ソロ練習とトリオ練習で身につけましょう。</p>
        </div>

        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
            <a href="{{ route('cases.index') }}" class="rounded-2xl bg-cyan-700 p-6 text-white shadow-sm transition hover:-translate-y-1 hover:bg-cyan-800">
                <span class="text-3xl">💬</span><h2 class="mt-5 text-xl font-bold">ケースドリル</h2><p class="mt-2 text-sm text-cyan-50">自分で答えた後、匿名で返し方を比べます。</p><span class="mt-6 inline-block text-sm font-semibold">ケースを選ぶ →</span>
            </a>
            <a href="{{ route('solo.index') }}" class="rounded-2xl bg-teal-700 p-6 text-white shadow-sm transition hover:-translate-y-1 hover:bg-teal-800">
                <span class="text-3xl">🎧</span><h2 class="mt-5 text-xl font-bold">ソロ練習</h2><p class="mt-2 text-sm text-teal-50">AIペルソナと一人で対話を練習します。</p><span class="mt-6 inline-block text-sm font-semibold">練習を始める →</span>
            </a>
            <a href="{{ route('trio.index') }}" class="rounded-2xl bg-indigo-700 p-6 text-white shadow-sm transition hover:-translate-y-1 hover:bg-indigo-800">
                <span class="text-3xl">👥</span><h2 class="mt-5 text-xl font-bold">トリオ練習</h2><p class="mt-2 text-sm text-indigo-50">3人で役割を交代しながら実践します。</p><span class="mt-6 inline-block text-sm font-semibold">ルームを作る →</span>
            </a>
            <a href="{{ route('quiz.index') }}" class="rounded-2xl bg-amber-500 p-6 text-white shadow-sm transition hover:-translate-y-1 hover:bg-amber-600">
                <span class="text-3xl">🧠</span><h2 class="mt-5 text-xl font-bold">クイズ</h2><p class="mt-2 text-sm text-amber-50">傾聴や質問の基礎を確認します。</p><span class="mt-6 inline-block text-sm font-semibold">学習を始める →</span>
            </a>
        </div>

        <div class="grid gap-5 md:grid-cols-3">
            <div class="rounded-2xl border border-zinc-200 bg-white p-5"><p class="text-sm text-zinc-500">ソロ完了</p><p class="mt-2 text-3xl font-bold">{{ $soloCount }}<span class="ml-1 text-base font-normal text-zinc-500">回</span></p></div>
            <div class="rounded-2xl border border-zinc-200 bg-white p-5"><p class="text-sm text-zinc-500">トリオ完了</p><p class="mt-2 text-3xl font-bold">{{ $trioCount }}<span class="ml-1 text-base font-normal text-zinc-500">回</span></p></div>
            <div class="rounded-2xl border border-zinc-200 bg-white p-5"><p class="text-sm text-zinc-500">クイズ最高得点</p><p class="mt-2 text-3xl font-bold">{{ $quizBest ?? 0 }}<span class="ml-1 text-base font-normal text-zinc-500">点</span></p></div>
        </div>
    </div>
</x-layouts::app>
