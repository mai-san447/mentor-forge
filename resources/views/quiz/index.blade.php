<x-layouts::app :title="__('クイズ')">
    <div class="mx-auto max-w-2xl space-y-6">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-600">QUIZ</p>
            <h1 class="mt-2 text-3xl font-bold text-zinc-900">メンタリング基礎クイズ</h1>
            <p class="mt-2 text-zinc-600">1問ずつ答えて、その場で解説を読みます。</p>
        </div>

        @if (! $question)
            <div class="rounded-2xl bg-amber-50 p-6 text-amber-900">クイズは準備中です。</div>
        @else
            {{-- 進み具合 --}}
            <div>
                <div class="flex items-baseline justify-between text-sm text-zinc-600">
                    <span>{{ $number }}問目 / 全{{ $total }}問</span>
                    <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs font-semibold text-zinc-700">{{ $question->category }}</span>
                </div>
                <div class="mt-2 h-2 overflow-hidden rounded-full bg-zinc-200">
                    <div class="h-full rounded-full bg-amber-500 transition-all" style="width: {{ (int) round($number / $total * 100) }}%"></div>
                </div>
            </div>

            <div class="rounded-2xl border border-zinc-200 bg-white p-6">
                <p class="text-lg font-bold text-zinc-900">{{ $question->question }}</p>

                <div class="mt-5 space-y-3">
                    @foreach ($question->choices as $choiceIndex => $choice)
                        @php
                            $isCorrect = $choiceIndex === $question->correct_index;
                            $isPicked = $selected === $choiceIndex;
                        @endphp

                        @if ($selected === null)
                            {{-- 未回答: 押せる選択肢 --}}
                            <form method="POST" action="{{ route('quiz.answer') }}">
                                @csrf
                                <input type="hidden" name="question_id" value="{{ $question->id }}">
                                <input type="hidden" name="choice" value="{{ $choiceIndex }}">
                                <button type="submit"
                                        class="flex w-full items-center gap-3 rounded-xl border border-zinc-200 bg-white p-4 text-left text-zinc-900 transition hover:border-amber-400 hover:bg-amber-50">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-zinc-100 text-sm font-bold text-zinc-600">{{ ['ア', 'イ', 'ウ', 'エ'][$choiceIndex] ?? $choiceIndex + 1 }}</span>
                                    <span>{{ $choice }}</span>
                                </button>
                            </form>
                        @else
                            {{-- 回答後: 正解と自分の選択が分かるように色を付ける --}}
                            <div @class([
                                'flex items-center gap-3 rounded-xl border p-4',
                                'border-teal-500 bg-teal-50 text-teal-900' => $isCorrect,
                                'border-rose-400 bg-rose-50 text-rose-900' => $isPicked && ! $isCorrect,
                                'border-zinc-200 bg-white text-zinc-400' => ! $isCorrect && ! $isPicked,
                            ])>
                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-white/70 text-sm font-bold">{{ ['ア', 'イ', 'ウ', 'エ'][$choiceIndex] ?? $choiceIndex + 1 }}</span>
                                <span>{{ $choice }}</span>
                                @if ($isCorrect)
                                    <span class="ml-auto shrink-0 text-sm font-bold">正解</span>
                                @elseif ($isPicked)
                                    <span class="ml-auto shrink-0 text-sm font-bold">あなたの回答</span>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>

                @if ($selected !== null)
                    <div class="mt-6 rounded-xl bg-zinc-50 p-5">
                        <p class="font-bold text-zinc-900">
                            {{ $selected === $question->correct_index ? '正解です' : 'おしいです' }}
                        </p>
                        <p class="mt-2 text-zinc-700">{{ $question->explanation }}</p>
                    </div>

                    <form method="POST" action="{{ route('quiz.next') }}" class="mt-6">
                        @csrf
                        <button type="submit" class="w-full rounded-xl bg-amber-500 px-5 py-3 font-bold text-white transition hover:bg-amber-600">
                            {{ $isLast ? '結果を見る' : '次の問題へ' }}
                        </button>
                    </form>
                @endif
            </div>

            <form method="POST" action="{{ route('quiz.restart') }}">
                @csrf
                <button type="submit" class="text-sm text-zinc-500 underline hover:text-zinc-700">最初からやり直す</button>
            </form>
        @endif
    </div>
</x-layouts::app>
