---
marp: true
theme: default
paginate: true
style: |
  section {
    font-family: 'Noto Sans JP', 'Hiragino Kaku Gothic ProN', sans-serif;
    font-size: 32px;
  }
  section.title {
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }
  section.big {
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: center;
    font-size: 2.2em;
  }
  section.big h2 {
    font-size: 1.4em;
  }
  section.big h3 {
    font-size: 0.8em;
  }
  section.mid {
    font-size: 40px;
  }
  section.timeline {
    font-size: 34px;
  }
  section.timeline h2 {
    border-bottom: 3px solid #2980b9;
    padding-bottom: 8px;
    color: #2980b9;
  }
---

<!-- HTML生成: npx @marp-team/marp-cli --no-stdin docs-11/slides.md --html -o docs-11/slides.html -->

<!-- _class: title -->

# 「フレームワークを作れば<br>開発力が上がる」で殴り抜けた話

<br>

## pika

<!--
「pikaです。独学でPHPフレームワークを自作した話をします。時系列で振り返ります」
-->

---

<!-- _class: timeline -->

## 2022年8月 ― プログラミングを始めた

Progateから独学でPHPを学び始めた

掲示板が作れるようになった

Webアプリケーションの処理自体は書けるようになった

<!--
「2022年8月、プログラミングを始めました。Progateから独学でPHPを学びました。掲示板が作れるようになって、機能は一通り書けるようになりました」
淡々と
-->

---

<!-- _class: timeline -->

## 2023年2月 ― コードが絡まっていた

```php
class PostReview extends ApiAbstract
{
    public function __construct()
    {
        $this->init($this->validation(), ...);
        $insert_db = new InsertDB($this->sql);
        $result = $this->add($insert_db, $post_value);
        httpResponse(200, 'OK');
    }
}
```

PHPファイル43個、6,171行、クラス16個。newするだけで全部走る

<!--
「2023年2月。半年でPHPファイルは43個、6,171行になっていました。OAuth 2.0もレート制限も画像処理も実装していました。でもコンストラクタにプログラムの全処理が入っていて、newするだけで全部走る構造でした」
事実を淡々と
-->

---

<!-- _class: big -->

## どこに何を書けばいいか分からない

### 何度も最初からやり直した

<!--
「どこに何を書けばいいか、分からなかったんです」
（間）
「何度も最初からやり直しました」
-->

---

<!-- _class: timeline -->

## 2023年2月 ― Laravelの記事を読んだ

「こう書くとこう動く」は分かった

**なぜそう書くのかは分からなかった**

ちょうどその頃、ChatGPT（GPT-3.5）が出た

ハルシネーションだらけだったが、壁打ち相手にはなった

<!--
「同じ頃、QiitaでLaravelの記事を読みました。こう書くとこう動くのは分かるんですが、なぜそう書くのかが分からない。ちょうどGPT-3.5が出まして、ハルシネーションだらけでしたけど壁打ち相手にはなりました」
さくさく
-->

---

<!-- _class: timeline -->

## 2023年2月 ― 決めた

アプリケーションの処理自体は書ける

どう整理すればいいかが分からなかった

**Laravelの使い方だけ見て、裏側を自分で作ることにした**

<!--
「処理自体は書けるんです。でもそれをどう整理すればいいかが分からなかった。それでLaravelの使い方だけ見て、裏側の仕組みを自分で作ることにしました」
-->

---

<!-- _class: timeline -->

## 2023年3月 ― ルーティングを作った

```
app/Controllers/Pages/
    ImagePageController.php   ← /image でアクセスされる
    IndexPageController.php   ← / でアクセスされる
```

まず困っていた「どこに何を書くか」を解決した

ファイルを置くだけ。設定ファイルは0行

<!--
「2023年3月。まず一番困っていた問題から解きました。URLとクラス名を対応させて、ファイルを置くだけでルーティングされるようにしました。設定ファイルは0行です」
-->

---

<!-- _class: timeline -->

## 2023年3月 ― 自動注入を作った

```php
class ImagePageController {
    public function __construct(ImageStore $store) {
        // ↑ 型を見て自動で new して渡す
    }
}
// 裏側: リフレクションで引数の型を読んで new する
```

Laravelのコントローラで引数にクラスが並んでいるのを見て、自分でも作りたくなった

<!--
「Laravelのコントローラを見たら、引数にクラスが並んでいて便利そうだった。ChatGPTに聞いたらリフレクションというものがあると教えてくれました」
-->

---

<!-- _class: timeline -->

## 2023年3月 ― 再帰に気づいた

```php
class ImageStore {
    public function __construct(Database $db) { ... }
}
class Database {
    public function __construct(Config $config) { ... }
}
// リフレクションを再帰にしただけで
// ImageStore → Database → Config 全部自動でnewされる
```

クラスが大きくなった。分割したくなった。**1行変えただけだった**

**問題が、仕組みを教えてくれた**

<!--
「クラスが大きくなりすぎたので分割したいと思ったとき、再帰にすればいいだけだと気づきました。1行変えただけで全部自動で解決されました」
ぼそっと「問題が、仕組みを教えてくれた」★1回目
-->

---

<!-- _class: title -->

## 2023年4月15日 ― MimimalCMS v0.1 完成

MVC / ルーティング / DIコンテナ / バリデーション
CSRF対策 / 自動XSSエスケープ / ヘルパー関数

外部ライブラリ依存ゼロ

<!--
「2023年4月15日。MimimalCMS v0.1が完成しました」
（間）
「決めてから約2ヶ月でした。問題が明確だったから、速かった」
読ませる
-->

---

## こう書くだけで動く

```php
public function store(
    GdImageFactoryInterface $image,  // 自動注入
    ImageStoreInterface $store,      // 自動注入
    array $file,                     // バリデーション済み
    string $imageType,               // バリデーション済み
) {
    // やりたいことだけ書く
}
```

設定ファイルなし、newなし

<!--
「全部合わさるとこうなります。設定ファイルなし、newなし。やりたいことだけ書けばいい」
一拍
-->

---

<!-- _class: timeline -->

## 2023年〜2026年 ― 実サービスで使い続けた

コントローラ 41個 / モデル 145個 / サービス 143個

コミット 3,300+ / PR 233本

今もユーザーがいる実サービス

<!--
「このフレームワークで実際にWebサービスを作って公開しました。2023年から今日まで、ずっと動いています」
数字はスライドに任せる
-->

---

<!-- _class: big -->

## 後から知った

<!--
「……後から知ったんですが」
（間を取る）
-->

---

## 自分が作ったもの、全部名前がついていた

「どこに何を書くか」の答え → 「設定より規約」と呼ばれていた
Convention over Configuration

newを手で書いて繋いでいた問題の答え → 「依存性の注入」と呼ばれていた
Dependency Injection

<!--
「自分が作ったもの、全部名前がついていました」
「どこに何を書くかの答えは設定より規約。newを手で繋いでいた問題の答えは依存性の注入」
淡々と
-->

---

<!-- _class: timeline -->

## 振り返り ― 8ヶ月の記録

| 時期 | できごと |
|---|---|
| 2022年8月 | プログラミング開始 |
| 2023年2月 | 43ファイル・6,171行に到達。限界 |
| 2023年2月 | フレームワーク自作を決意 |
| 2023年4月 | MimimalCMS v0.1 完成 |
| 2023年〜 | 実サービスで3,300+コミット |

速かったのは才能ではない。**問題が明確だったから**

<!--
「8ヶ月の記録です。速かったのは才能ではありません。6,171行のコードと格闘して、問題が明確だったから速かった」
-->

---

<!-- _class: big -->

# 問題が、設計を導いた

AIが正しい答えをくれたわけじゃない

問題を持っていたから、設計に辿り着いた

<!--
「AIが正しい答えをくれたわけじゃありません。問題を持っていたから、設計に辿り着けたんです」
はっきり「問題が、設計を導いた」★2回目
-->

---

<!-- _class: big -->

# 問題を持て、AIに聞け、自分で作れ

### 今のAIならもっとできる

ありがとうございました

<!--
「問題を持ってください。AIに聞いてください。自分で作ってください。今のAIならもっとできます」
「ありがとうございました」
-->
