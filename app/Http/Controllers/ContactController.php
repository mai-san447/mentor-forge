<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function create(Request $request): View
    {
        // 送信までにかかった時間を測るための基準。自動入力は人間より圧倒的に速い。
        $request->session()->put('contact_opened_at', now()->timestamp);

        return view('contact.index');
    }

    public function store(Request $request): RedirectResponse
    {
        // 1層目: ハニーポット。画面上見えない項目なので、埋まっていれば人ではない。
        // 弾いたことを気づかせないよう、成功したときと同じ画面を返す。
        if (filled($request->input('website'))) {
            return $this->accepted();
        }

        // 2層目: 送信が速すぎるものを弾く。
        // セッションが無い場合（Cookieを拒否している等）は正規の利用者を締め出さないよう通す。
        $openedAt = $request->session()->pull('contact_opened_at');
        if ($openedAt !== null && now()->timestamp - (int) $openedAt < (int) config('contact.min_seconds')) {
            return back()
                ->withInput()
                ->withErrors(['body' => '送信が早すぎます。少し時間をおいてもう一度お試しください。']);
        }

        // 3層目のレート制限はルート側の throttle で効かせている。

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
            'body' => ['required', 'string', 'max:1000'],
        ], [], [
            'name' => 'お名前',
            'email' => 'メールアドレス',
            'body' => 'お問い合わせ内容',
        ]);

        ContactMessage::create([
            ...$validated,
            'ip_address' => $request->ip(),
        ]);

        return $this->accepted();
    }

    public function inbox(Request $request): View
    {
        $adminEmail = config('contact.admin_email');

        // ロールの仕組みを持たないため、設定したメールアドレスとの一致で判定する。
        // 未設定なら誰も開けない。
        abort_unless(filled($adminEmail) && $request->user()->email === $adminEmail, 403);

        return view('contact.inbox', [
            'messages' => ContactMessage::latest()->limit(100)->get(),
        ]);
    }

    private function accepted(): RedirectResponse
    {
        return redirect()
            ->route('contact.create')
            ->with('status', 'お問い合わせを受け付けました。ご連絡ありがとうございます。');
    }
}
