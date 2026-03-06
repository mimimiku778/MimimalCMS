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
  section.reveal {
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: center;
    font-size: 1.6em;
  }
---

<!-- HTML生成: npx @marp-team/marp-cli --no-stdin docs-6/slides.md --html -o docs-6/slides.html -->

<!-- _class: title -->

# 「フレームワークを作れば<br>開発力が上がる」で殴り抜けた話

<br>

## pika

<!--
「pikaです。今日は、自作PHPフレームワークの話をします」
-->

---

## このフレームワークで動いているWebサービスがある

コントローラ 41個 / モデル 145個 / サービス 143個

コミット 3,300+ / PR 233本

今もユーザーがいる実サービス

<!--
「まず最初にお見せしたいものがあります。このフレームワークで動いているWebサービスがあります。コントローラ41個、モデル145個。今もユーザーがいる実サービスです」
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
「コントローラはこう書くだけです。設定ファイルなし、newなし。やりたいことだけ書けばいい」
一拍
-->

---

## ルーティングもバリデーションも1行で書ける

```php
Route::path('image/store@post')           // POST /image/store
    ->matchFile('file', ['image/jpeg'])   // 画像だけ許可
    ->matchStr('imageType', regex: '/.+/')// 文字列チェック
    ->matchNum('imageSize', max: 1000);   // 数値の範囲チェック
```

ルーティング、バリデーション、エラー処理。これだけ。

<!--
「ルーティングもバリデーションもこれだけです」
さっと
-->

---

## フレームワークの機能

MVC / ルーティング / DIコンテナ / バリデーション
CSRF対策 / 自動XSSエスケープ / ヘルパー関数

外部ライブラリ依存ゼロ

<!--
「MVC、ルーティング、DIコンテナ、バリデーション。一通り揃っています。外部ライブラリ依存ゼロです」
止まる。読ませる。
-->

---

<!-- _class: big -->

## 後から知ったんですが

<!--
「……後から知ったんですが」
黙る。★急がない
-->

---

## 自分が作ったもの、全部名前がついていた

「どこに何を書くか」の答え → 「設定より規約」と呼ばれていた
Convention over Configuration

newを手で書いて繋いでいた問題の答え → 「依存性の注入」と呼ばれていた
Dependency Injection

<!--
「自分が作ったもの、全部名前がついていました。設定より規約。依存性の注入。知らずに再発明していたんです」
淡々と
-->

---

<!-- _class: reveal -->

## 実はプログラミングを始めたのは
## その8ヶ月前でした

<!--
「……実はプログラミングを始めたのは、その8ヶ月前でした」
黙る。反応を待つ。
-->

---

<!-- _class: mid -->

## 8ヶ月前、Progateから始めた

掲示板が作れるようになった

機能を足すたびにコードが絡まった

直そうとするともっと絡まった

<!--
「Progateから始めて、掲示板が作れるようになりました。でも機能を足すたびにコードが絡まって、直そうとするともっと絡まる」
-->

---

## 当時のコード

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

newするだけで認証からレスポンスまで全部走る

<!--
「当時のコードです。コンストラクタの中にプログラム全体が入っている。newするだけで全部走る」
-->

---

<!-- _class: big -->

## どこに何を書けばいいか分からない

### 何度も最初からやり直した

<!--
「どこに何を書けばいいか、分からなかったんです」
黙る。読ませる。
「何度も最初からやり直しました」
-->

---

<!-- _class: mid -->

## QiitaでLaravelの記事を読んだ

「こう書くとこう動く」は分かった

### なぜそう書くのかは分からなかった

ちょうどその頃、ChatGPT（GPT-3.5）が出た

ハルシネーションだらけだったが、壁打ち相手にはなった

<!--
「QiitaでLaravelの記事を読みました。なぜそう書くのかが分からなかった。ちょうどGPT-3.5が出て、壁打ち相手にはなりました」
さくさく
-->

---

<!-- _class: title -->

## 使い方だけ見て、裏側を自分で作ることにした

<!--
「それで、Laravelの使い方だけ見て、裏側を自分で作ることにしました」
-->

---

## ファイルを置くだけでルーティングされる

```
app/Controllers/Pages/
    ImagePageController.php   ← /image でアクセスされる
    IndexPageController.php   ← / でアクセスされる
```

設定ファイルは0行

<!--
「まずURLとクラス名を対応させて、ファイルを置くだけでルーティングされるようにしました」
-->

---

## リフレクションで引数の型を読んで自動でnewする

```php
class ImagePageController {
    public function __construct(ImageStore $store) {
        // ↑ 型を見て自動で new して渡す
    }
}
```

手でnewしなくていい

<!--
「リフレクションで引数の型を読んで、自動でnewするようにしました。手でnewしなくていい」
さっと
-->

---

## ある日気づいた

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

1行変えただけだった

**問題が、仕組みを教えてくれた**

<!--
「クラスが大きくなりすぎて分割したいと思ったとき、再帰にすればいいだけだと気づきました。1行変えただけで全部自動で解決された」
ぼそっと「問題が、仕組みを教えてくれた」★1回目
-->

---

<!-- _class: big -->

# 問題が、設計を導いた

AIが正しい答えをくれたわけじゃない

問題を持っていたから、設計に辿り着いた

<!--
「さっきお見せしたフレームワーク、あの機能は全部こうやってできました。AIが正しい答えをくれたわけじゃありません。問題を持っていたから、設計に辿り着けたんです」
はっきり。★2回目
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
