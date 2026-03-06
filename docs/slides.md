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
---

<!-- HTML生成: npx @marp-team/marp-cli --no-stdin docs/slides.md --html -o docs/slides.html -->

<!-- _class: title -->

# 「フレームワークを作れば<br>開発力が上がる」で殴り抜けた話

<br>

## pika

<!--
「pikaです。独学でPHPフレームワークを自作した話をします」

繰り返しフレーズ（ぼそっと→はっきり）
「問題が、仕組みを教えてくれた」
「問題が、設計を導いた」
-->

---

## Progateから独学で始めて、一応作れるようになった

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
「Progateから始めて、掲示板が作れるようになりました。でも機能を足すたびにコードが絡まって、直そうとするともっと絡まる」
一拍
-->

---

<!-- _class: big -->

## どこに何を書けばいいか分からない

### 何度も最初からやり直した

<!--
「どこに何を書けばいいか、分からなかったんです」
黙る。読ませる。
「何度も最初からやり直しました」
★急がない
-->

---

<!-- _class: mid -->

## QiitaでLaravelの記事を読んだ

「こう書くとこう動く」は分かった

### なぜそう書くのかは分からなかった

ちょうどその頃、ChatGPT（GPT-3.5）が出た

ハルシネーションだらけだったが、壁打ち相手にはなった

<!--
「QiitaでLaravelの記事を読みました。こう書くとこう動くのは分かるんですが、なぜそう書くのかが分からなかった」
「ちょうどその頃GPT-3.5が出まして、ハルシネーションだらけでしたけど壁打ち相手にはなりました」
さくさく
-->

---

<!-- _class: title -->

## アプリケーションの処理自体は書ける
## どう整理すればいいかが分からなかった

### Laravelの使い方だけ見て、裏側を自分で作ることにした

<!--
「アプリケーションの処理自体は書けるんです。でもそれをどう整理すればいいかが分からなかった。それでLaravelの使い方だけ見て、裏側の仕組みを自分で作ることにしました」
-->

---

## まず困っていたのは「どこに何を書くか」

```
app/Controllers/Pages/
    ImagePageController.php   ← /image でアクセスされる
    IndexPageController.php   ← / でアクセスされる
```

ファイルを置くだけ。設定ファイルは0行

<!--
「まず一番困っていたのは、どこに何を書くかでした。そこでURLとクラス名を対応させて、ファイルを置くだけでルーティングされるようにしました。設定ファイルは0行です」
-->

---

## 次に、Laravelのコントローラを見て思った

引数にクラスが並んでいる。手でnewしているわけじゃないらしい

便利だな。作りたい

<!--
「次に、Laravelのコントローラを見たら、引数にクラスが並んでいて、手でnewしているわけじゃないらしい。これは便利だなと思って、自分でも作りたくなりました」
-->

---

## 型を書くだけで使える

```php
class ImagePageController {
    public function __construct(ImageStore $store) {
        // ↑ 型を見て自動で new して渡す
    }
}
// 裏側: リフレクションで引数の型を読んで new する
```

手でnewしなくていい

<!--
「引数の型を自動で読み取る方法はないかとChatGPTに聞いたところ、リフレクションというものがあると教えてくれました。これで何でもできるなと思いました」
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
「それまでは1つのクラスに別のクラスを注入しているだけで、その先の依存までは考えが及んでいませんでした。しかしあるとき、クラスが大きくなりすぎたので分割したいと思ったんです。そこで依存がすべて自動で解決されたら便利だなと考えて、再帰にすればいいだけだと気づきました」
「1行変えただけで全部自動で解決されました」←力入れる
ぼそっと「問題が、仕組みを教えてくれた」★1回目
-->

---

<!-- _class: title -->

# できた

MVC / ルーティング / DIコンテナ / バリデーション
CSRF対策 / 自動XSSエスケープ / ヘルパー関数

外部ライブラリ依存ゼロ

<!--
「で、できました」
止まる。読ませる。「外部依存ゼロ」は口で言わない。
-->

---

## こう書くだけで動く

```php
Route::path('image/store@post')           // POST /image/store
    ->matchFile('file', ['image/jpeg'])   // 画像だけ許可
    ->matchStr('imageType', regex: '/.+/')// 文字列チェック
    ->matchNum('imageSize', max: 1000);   // 数値の範囲チェック
```

ルーティング、バリデーション、エラー処理。これだけ。

<!--
「こう書くだけでルーティング、バリデーション、エラー処理が全部動きます」
さっと
-->

---

## 全部合わさるとこうなる

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

<!-- _class: title -->

## このフレームワークで
## 実際にWebサービスを作って公開した

<!--
「このフレームワークで実際にWebサービスを作って公開しました」
さっと
-->

---

## 今も動いている

コントローラ 41個 / モデル 145個 / サービス 143個

コミット 3,300+ / PR 233本

今もユーザーがいる実サービス

<!--
「今も動いています」
数字はスライドに任せる
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
「自分が作ったもの、全部名前がついていました」
「どこに何を書くかの答えは、設定より規約と呼ばれていました」
「newを手で繋いでいた問題の答えは、依存性の注入と呼ばれていました」
淡々と
-->

---

<!-- _class: big -->

# 問題が、設計を導いた

AIが正しい答えをくれたわけじゃない

問題を持っていたから、設計に辿り着いた

<!--
「AIが正しい答えをくれたわけじゃありません。問題を持っていたから、設計に辿り着けたんです」
一拍。「問題が、設計を導いた」はっきり。★2回目
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
