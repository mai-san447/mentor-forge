<?php

namespace Database\Seeders;

use App\Models\Persona;
use App\Models\QuizQuestion;
use App\Models\Scenario;
use Illuminate\Database\Seeder;

class MentorForgeSeeder extends Seeder
{
    public function run(): void
    {
        $persona = Persona::firstOrCreate(['name' => '佐藤 美咲'], [
            'role' => '入社2年目・営業職', 'background' => '仕事は丁寧だが、最近ミスが続いて自信を失っている。',
            'challenge' => '最近のミスと、周りに相談できずに抱え込んでいる', 'tone' => '慎重で、最初は少し遠慮がち', 'accent_color' => '#0f766e',
        ]);
        Scenario::firstOrCreate(['title' => '自信を失っているメンティとの1on1'], [
            'persona_id' => $persona->id, 'situation' => '最近ミスが続き、本人が相談を避けている状況です。',
            'goal' => '安心して話せる関係をつくり、本人の次の一歩を引き出す', 'difficulty' => '初級',
        ]);
        foreach ([
            ['category' => '傾聴', 'question' => '相手が話し始めたとき、最初に意識したいことは？', 'choices' => ['すぐに解決策を伝える', '相手の話を遮らずに聴く', '自分の体験を話す', '話題を変える'], 'correct_index' => 1, 'explanation' => 'まずは遮らずに聴き、相手が安心して話せる状態をつくります。'],
            ['category' => '質問', 'question' => '相手の考えを引き出しやすい質問は？', 'choices' => ['なぜできないの？', 'どうすべきか分かる？', 'そのとき、どんなことを感じましたか？', '私ならこうします'], 'correct_index' => 2, 'explanation' => '感情や経験を尋ねる質問は、内省を促します。'],
            ['category' => '共感', 'question' => '相手が不安を話したときの返答として適切なのは？', 'choices' => ['気にしすぎです', '大丈夫、誰でもあります', 'それは不安になりますよね', 'もっと頑張りましょう'], 'correct_index' => 2, 'explanation' => 'まず感情を受け止めてから、一緒に整理していきます。'],
        ] as $question) { QuizQuestion::firstOrCreate(['question' => $question['question']], $question); }
    }
}
