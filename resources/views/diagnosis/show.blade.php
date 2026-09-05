<x-layouts::app title="対話の振り返り診断">
    <div class="mx-auto max-w-4xl space-y-6">
        <div><p class="text-sm font-semibold text-teal-700">対話の振り返り</p><h1 class="mt-1 text-3xl font-bold">利用前後の変化を見つける</h1><p class="mt-2 text-zinc-600">能力を採点するテストではありません。今の自分に近い感覚を選び、変化を振り返るための記録です。</p></div>

        @if(session('status'))<div class="rounded-xl border border-teal-200 bg-teal-50 p-4 text-sm font-semibold text-teal-900">{{ session('status') }}</div>@endif
        @error('diagnosis')<div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-800">{{ $message }}</div>@enderror

        @if($pre && $post)
            <section class="rounded-2xl border border-zinc-200 bg-white p-5 sm:p-7">
                <h2 class="text-2xl font-bold">あなたが感じた変化</h2>
                <p class="mt-2 text-sm text-zinc-600">良し悪しではなく、利用前と今の自己認識の違いを項目ごとに表示しています。</p>
                <div class="mt-5 divide-y divide-zinc-100">
                    @foreach($items as $key => $label)
                        @php($difference = $post->responses[$key] - $pre->responses[$key])
                        <div class="py-4"><p class="font-semibold">{{ $label }}</p><div class="mt-2 flex flex-wrap items-center gap-2 text-sm"><span class="rounded-full bg-zinc-100 px-3 py-1">利用前：{{ $choices[$pre->responses[$key]] }}</span><span aria-hidden="true">→</span><span class="rounded-full bg-teal-50 px-3 py-1 text-teal-900">利用後：{{ $choices[$post->responses[$key]] }}</span><span class="font-semibold text-zinc-600">{{ $difference > 0 ? '以前よりそう感じる' : ($difference < 0 ? '以前ほどはそう感じない' : '変化なし') }}</span></div></div>
                    @endforeach
                </div>
            </section>
        @elseif($phase === 'post' && !$canTakePost)
            <section class="rounded-2xl border border-amber-200 bg-amber-50 p-6"><h2 class="text-xl font-bold">次はケースドリルを試しましょう</h2><p class="mt-2 text-zinc-700">利用前の記録は保存済みです。ケースに回答し、異なる返し方との比較と「次に試す行動」を記録すると、利用後の再診断ができます。</p><a href="{{ route('cases.index') }}" class="mt-4 inline-block rounded-xl bg-teal-700 px-5 py-3 font-semibold text-white">ケースを選ぶ</a></section>
        @else
            <form method="POST" action="{{ route('diagnosis.store') }}" class="space-y-5">
                @csrf<input type="hidden" name="phase" value="{{ $phase }}">
                <section class="rounded-2xl border border-zinc-200 bg-white p-5 sm:p-7"><h2 class="text-2xl font-bold">{{ $phase === 'pre' ? '利用前の記録' : '利用後の再診断' }}</h2><p class="mt-2 text-sm text-zinc-600">最近の対話場面を思い浮かべて答えてください。</p>
                    <div class="mt-6 space-y-7">
                        @foreach($items as $key => $label)
                            <fieldset><legend class="font-semibold text-zinc-900">{{ $loop->iteration }}. {{ $label }}</legend><div class="mt-3 grid gap-2 sm:grid-cols-5">@foreach($choices as $value => $choice)<label class="flex cursor-pointer gap-2 rounded-xl border border-zinc-200 p-3 text-sm hover:border-teal-500"><input type="radio" name="responses[{{ $key }}]" value="{{ $value }}" required @checked((string) old("responses.$key") === (string) $value) class="mt-0.5 text-teal-700"><span>{{ $choice }}</span></label>@endforeach</div>@error("responses.$key")<p class="mt-2 text-sm font-semibold text-red-700">{{ $message }}</p>@enderror</fieldset>
                        @endforeach
                    </div>
                </section>
                <button class="w-full rounded-xl bg-teal-700 px-5 py-3 font-semibold text-white hover:bg-teal-800 sm:w-auto">{{ $phase === 'pre' ? '利用前の状態を保存する' : '再診断を保存して変化を見る' }}</button>
            </form>
        @endif
    </div>
</x-layouts::app>
