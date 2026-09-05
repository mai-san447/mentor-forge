<x-layouts::app :title="__('Mentor Forge')">
    <div class="space-y-8">
        @if($isPilotTester)<div class="rounded-xl border border-violet-300 bg-violet-50 p-4 text-sm font-semibold text-violet-900">パイロットテストモードです。2週間後・4週間後の記録を今日確認できます。外部テスト前に解除してください。</div>@endif
        @if(session('status'))<div class="rounded-xl border border-teal-200 bg-teal-50 p-4 text-sm font-semibold text-teal-900">{{ session('status') }}</div>@endif
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-teal-600">MENTOR FORGE</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight">学んで、試して、実践する</h1>
            <p class="mt-2 max-w-2xl text-zinc-600">メンタリングの知識をクイズで学び、ソロ練習とトリオ練習で身につけましょう。</p>
        </div>

        <section class="rounded-2xl border border-teal-200 bg-teal-50 p-5 sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"><div><h2 class="text-xl font-bold">対話の変化を振り返る</h2><p class="mt-1 text-sm text-zinc-600">{{ !$preDiagnosis ? 'まず利用前の感覚を記録します。' : (!$postDiagnosis ? 'ケース学習後に再診断すると、項目ごとの変化が見えます。' : '利用前後の変化を項目別に確認できます。') }}</p></div><a href="{{ route('diagnosis.show') }}" class="shrink-0 rounded-xl bg-teal-700 px-5 py-3 text-center font-semibold text-white">{{ !$preDiagnosis ? '利用前診断を始める' : (!$postDiagnosis ? '再診断へ' : '変化を見る') }}</a></div>
        </section>

        @if($followUpSchedule->isNotEmpty())
            <section class="rounded-2xl border border-zinc-200 bg-white p-5 sm:p-6"><h2 class="text-xl font-bold">これからの振り返り</h2><p class="mt-1 text-sm text-zinc-600">ケースで決めた行動を、2週間後・4週間後に現場の出来事と結びつけて記録します。</p><div class="mt-4 divide-y divide-zinc-100">@foreach($followUpSchedule as $item)<a href="{{ route('follow-ups.show', [$item['reflection'], $item['weeks']]) }}" class="flex flex-col gap-2 py-4 first:pt-0 last:pb-0 sm:flex-row sm:items-center sm:justify-between"><div><p class="font-semibold">{{ $item['reflection']->scenario->title }}・{{ $item['weeks'] }}週間後</p><p class="mt-1 line-clamp-1 text-sm text-zinc-600">試す行動：{{ $item['reflection']->next_action }}</p></div><span class="shrink-0 rounded-full px-3 py-1 text-sm font-semibold {{ $item['completed'] ? 'bg-teal-100 text-teal-800' : ($item['due_at']->isPast() ? 'bg-amber-100 text-amber-900' : 'bg-zinc-100 text-zinc-700') }}">{{ $item['completed'] ? '記録済み' : ($item['due_at']->isPast() ? '記録できます' : $item['due_at']->format('Y/m/d').'から') }}</span></a>@endforeach</div></section>
        @endif

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

        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-zinc-200 bg-white p-5"><p class="text-sm text-zinc-500">ソロ完了</p><p class="mt-2 text-3xl font-bold">{{ $soloCount }}<span class="ml-1 text-base font-normal text-zinc-500">回</span></p></div>
            <div class="rounded-2xl border border-zinc-200 bg-white p-5"><p class="text-sm text-zinc-500">トリオ完了</p><p class="mt-2 text-3xl font-bold">{{ $trioCount }}<span class="ml-1 text-base font-normal text-zinc-500">回</span></p></div>
            <div class="rounded-2xl border border-zinc-200 bg-white p-5"><p class="text-sm text-zinc-500">クイズ最高得点</p><p class="mt-2 text-3xl font-bold">{{ $quizBest ?? 0 }}<span class="ml-1 text-base font-normal text-zinc-500">点</span></p></div>
            <div class="rounded-2xl border border-zinc-200 bg-white p-5"><p class="text-sm text-zinc-500">ケース振り返り</p><p class="mt-2 text-3xl font-bold">{{ $caseReflectionCount }}<span class="ml-1 text-base font-normal text-zinc-500">件</span></p></div>
        </div>

        <section class="rounded-2xl border border-zinc-200 bg-white p-5 sm:p-6">
            <h2 class="text-xl font-bold">ケースドリルの学習履歴</h2>
            <div class="mt-4 divide-y divide-zinc-100">
                @forelse($recentCaseReflections as $reflection)
                    <a href="{{ route('cases.show', $reflection->scenario) }}" class="flex flex-col gap-1 py-4 first:pt-0 last:pb-0 sm:flex-row sm:items-center sm:justify-between">
                        <div><p class="font-semibold text-zinc-900">{{ $reflection->scenario->title }}</p><p class="mt-1 text-sm text-zinc-600">次に試す：{{ $reflection->next_action }}</p></div>
                        <span class="text-sm text-teal-700">{{ $reflection->created_at->format('Y/m/d') }} →</span>
                    </a>
                @empty
                    <p class="py-3 text-sm text-zinc-600">まだ振り返りはありません。ケースドリルで最初の1件を記録しましょう。</p>
                @endforelse
            </div>
        </section>
    </div>
</x-layouts::app>
