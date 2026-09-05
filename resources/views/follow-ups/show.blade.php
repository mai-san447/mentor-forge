<x-layouts::app :title="$weeks.'週間後の実践記録'">
    <div class="mx-auto max-w-3xl space-y-6">
        <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-teal-700">← マイページへ</a>
        <div><p class="text-sm font-semibold text-indigo-700">{{ $weeks }}週間後の振り返り</p><h1 class="mt-1 text-3xl font-bold">現場で何が起きましたか？</h1><p class="mt-2 text-zinc-600">成果の採点ではなく、試したことと相手との間に起きた変化を残します。</p></div>
        <section class="rounded-2xl border border-indigo-200 bg-indigo-50 p-5 sm:p-7"><p class="text-sm font-semibold text-indigo-700">{{ $reflection->scenario->title }}</p><h2 class="mt-2 font-semibold">試すと決めた行動</h2><p class="mt-1 whitespace-pre-wrap leading-7">{{ $reflection->next_action }}</p><p class="mt-4 text-sm text-zinc-600">記録時期：{{ $dueAt->format('Y年n月j日') }}以降</p></section>

        @if($followUp)
            <section class="rounded-2xl border border-teal-200 bg-white p-5 sm:p-7"><h2 class="text-xl font-bold">記録済み</h2><dl class="mt-4 space-y-4"><div><dt class="font-semibold">実践</dt><dd>{{ $followUp->practiced ? '試した' : 'まだ試していない' }}</dd></div>@if($followUp->counterpart_reaction)<div><dt class="font-semibold">相手の反応</dt><dd class="whitespace-pre-wrap">{{ $followUp->counterpart_reaction }}</dd></div>@endif @if($followUp->consultation_change)<div><dt class="font-semibold">相談の深まり</dt><dd>{{ ['deeper' => '以前より深まった', 'same' => '大きな変化はなかった', 'shallower' => '話が浅くなった・続かなかった', 'unknown' => 'まだわからない'][$followUp->consultation_change] }}</dd></div>@endif @if($followUp->note)<div><dt class="font-semibold">メモ</dt><dd class="whitespace-pre-wrap">{{ $followUp->note }}</dd></div>@endif</dl></section>
        @elseif(!$isDue)
            <div class="rounded-2xl border border-dashed border-zinc-300 bg-zinc-100 p-6 text-center text-zinc-700">この記録は{{ $dueAt->format('Y年n月j日') }}から入力できます。まずは現場で無理なく試してみてください。</div>
        @else
            <form method="POST" action="{{ route('follow-ups.store', [$reflection, $weeks]) }}" class="rounded-2xl border border-zinc-200 bg-white p-5 sm:p-7">
                @csrf
                <div class="space-y-6"><fieldset><legend class="font-semibold">決めた行動を試しましたか？</legend><div class="mt-3 flex gap-4"><label><input type="radio" name="practiced" value="1" required @checked(old('practiced') === '1')> 試した</label><label><input type="radio" name="practiced" value="0" required @checked(old('practiced') === '0')> まだ試していない</label></div></fieldset>
                    <div><label for="counterpart_reaction" class="font-semibold">相手はどんな反応をしましたか？</label><textarea id="counterpart_reaction" name="counterpart_reaction" maxlength="2000" rows="4" class="mt-2 block w-full rounded-xl border-zinc-300" placeholder="試した場合は、表情・言葉・会話の続き方などを記録">{{ old('counterpart_reaction') }}</textarea>@error('counterpart_reaction')<p class="mt-2 text-sm font-semibold text-red-700">{{ $message }}</p>@enderror</div>
                    <fieldset><legend class="font-semibold">相談の深まりはどうでしたか？</legend><div class="mt-3 grid gap-2 sm:grid-cols-2">@foreach(['deeper' => '以前より深まった', 'same' => '大きな変化はなかった', 'shallower' => '話が浅くなった・続かなかった', 'unknown' => 'まだわからない'] as $value => $label)<label class="rounded-xl border border-zinc-200 p-3"><input type="radio" name="consultation_change" value="{{ $value }}" @checked(old('consultation_change') === $value)> {{ $label }}</label>@endforeach</div>@error('consultation_change')<p class="mt-2 text-sm font-semibold text-red-700">{{ $message }}</p>@enderror</fieldset>
                    <div><label for="note" class="font-semibold">次に向けたメモ（任意）</label><textarea id="note" name="note" maxlength="2000" rows="3" class="mt-2 block w-full rounded-xl border-zinc-300">{{ old('note') }}</textarea></div>
                    @error('follow_up')<p class="text-sm font-semibold text-red-700">{{ $message }}</p>@enderror<button class="w-full rounded-xl bg-indigo-700 px-5 py-3 font-semibold text-white sm:w-auto">実践記録を保存する</button>
                </div>
            </form>
        @endif
    </div>
</x-layouts::app>
