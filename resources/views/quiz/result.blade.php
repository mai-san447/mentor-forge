<x-layouts::app :title="__('クイズ結果')">
    <div class="mx-auto max-w-2xl space-y-6">
        <div class="rounded-2xl bg-amber-500 p-8 text-white">
            <p class="text-sm opacity-80">QUIZ RESULT</p>
            <h1 class="mt-2 text-3xl font-bold">お疲れさまでした</h1>
            <p class="mt-4 text-5xl font-bold">
                {{ $attempt->score }}<span class="text-xl font-normal"> / {{ $attempt->total }} 問正解</span>
            </p>
        </div>

        <div class="space-y-3">
            @foreach ($questions as $question)
                @php
                    $picked = $attempt->answers[$question->id] ?? null;
                    $isCorrect = $picked === $question->correct_index;
                @endphp

                <div class="rounded-xl border border-zinc-200 bg-white p-5">
                    <div class="flex items-start gap-3">
                        <span @class([
                            'mt-1 shrink-0 rounded-full px-2 py-0.5 text-xs font-bold',
                            'bg-teal-100 text-teal-800' => $isCorrect,
                            'bg-rose-100 text-rose-800' => ! $isCorrect,
                        ])>{{ $isCorrect ? '正解' : '不正解' }}</span>
                        <div>
                            <p class="font-semibold text-zinc-900">{{ $question->question }}</p>
                            <p class="mt-2 text-sm text-zinc-600">正解: {{ $question->choices[$question->correct_index] ?? '' }}</p>
                            <p class="mt-2 text-sm text-zinc-600">{{ $question->explanation }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex flex-wrap gap-3">
            <form method="POST" action="{{ route('quiz.restart') }}">
                @csrf
                <button type="submit" class="rounded-xl bg-amber-500 px-5 py-3 font-bold text-white transition hover:bg-amber-600">もう一度挑戦する</button>
            </form>
            <a class="rounded-xl border border-zinc-300 px-5 py-3 font-bold text-zinc-700 transition hover:bg-zinc-50" href="{{ route('dashboard') }}">ダッシュボードへ</a>
        </div>
    </div>
</x-layouts::app>
