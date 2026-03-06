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
  section.question {
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: center;
    font-size: 1.8em;
  }
  section.question h2 {
    color: #2563eb;
  }
---

<!-- _class: title -->

# 「フレームワークを作れば<br>開発力が上がる」で殴り抜けた話

<br>

## pika

<!--
「pikaです。今日はいくつか質問をしながら話を進めます」
-->

---

<!-- _class: question -->

## 皆さんは
## 「どこに何を書くか」で
## 迷ったことはありますか？

<!--
「皆さんは、どこに何を書けばいいか、迷ったことはありますか？」
2秒待つ。会場を見渡す
「僕はずっと迷っていました」
-->

---

## Progateから始めて、一応作れるようになった

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

動く。動くけど、足すたびに壊れる

<!--
「Progateから独学で始めて、掲示板とかAPIとか、一応作れるようになりました。でも機能を足すたびに壊れるんです。何度も最初からやり直しました」
-->

---

<!-- _class: question -->

## Laravelの記事を読んで
## 「なぜそう書くか」まで
## 理解できましたか？

<!--
「皆さんはフレームワークの記事を読んで、なぜそう書くかまで理解できましたか？」
一拍
「僕はできませんでした」
-->

---

<!-- _class: mid -->

## 「こう書くとこう動く」は分かった

### なぜそう書くのかは分からなかった

ちょうどGPT-3.5が出た。壁打ち相手にはなった

<!--
「こう書くとこう動くのは分かるんです。でもなぜそう書くかが分からなかった。ちょうどGPT-3.5が出て、壁打ち相手にしていました」
-->

---

<!-- _class: question -->

## もし裏側の仕組みを
## 自分で作ったら
## 理解できると思いますか？

<!--
「もし裏側の仕組みを自分で作ったら、理解できると思いますか？」
一拍
「僕はそう賭けました。Laravelの使い方だけ見て、裏側を自分で作ることにしました」
-->

---

## 「どこに何を書くか」の答え

```
app/Controllers/Pages/
    ImagePageController.php   ← /image でアクセスされる
    IndexPageController.php   ← / でアクセスされる
```

ファイルを置くだけ。設定ファイルは0行

<!--
「URLとクラス名を対応させて、ファイルを置くだけでルーティングされるようにしました。設定ファイルは0行です」
-->

---

<!-- _class: question -->

## コントローラの引数に
## クラスが並んでいるの
## 見たことありますよね？

<!--
「Laravelのコントローラの引数にクラスが並んでいるの、見たことありますよね？」
「手でnewしているわけじゃないらしい。便利だな、作りたいなと思いました」
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

ChatGPTに聞いてリフレクションを知った

<!--
「ChatGPTに聞いたらリフレクションというものがあると教えてくれました。引数の型を実行時に読めるんです」
-->

---

<!-- _class: question -->

## 依存の先に依存があったら
## どうしますか？

<!--
「ここで質問です。依存の先にさらに依存があったら、どうしますか？」
2秒待つ
-->

---

## 再帰にしただけだった

```php
class ImageStore {
    public function __construct(Database $db) { ... }
}
class Database {
    public function __construct(Config $config) { ... }
}
// ImageStore → Database → Config 全部自動でnewされる
```

1行変えただけ

**問題が、仕組みを教えてくれた**

<!--
「クラスが大きくなって分割したかったんです。再帰にすればいいだけでした。1行変えただけです」
ぼそっと「問題が、仕組みを教えてくれた」★1回目
-->

---

<!-- _class: title -->

# できた

MVC / ルーティング / DIコンテナ / バリデーション
CSRF対策 / 自動XSSエスケープ / ヘルパー関数

外部ライブラリ依存ゼロ

<!--
「できました」
止まる。読ませる。
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
-->

---

## 実サービスで動いている

コントローラ 41個 / モデル 145個 / サービス 143個

コミット 3,300+ / PR 233本

今もユーザーがいる実サービス

<!--
「このフレームワークで実際にWebサービスを作って公開しました。今も動いています」
-->

---

<!-- _class: question -->

## 僕が作ったこれらに
## 名前がついていたこと
## 知っていますか？

<!--
「ここで最後の質問です。僕が作ったこれらに、名前がついていたこと、皆さんは知っていますか？」
一拍
-->

---

## 全部、名前がついていた

「どこに何を書くか」の答え → **Convention over Configuration**（設定より規約）

newを手で書いて繋いでいた問題の答え → **Dependency Injection**（依存性の注入）

<!--
「設定より規約。依存性の注入。全部、名前がついていました。僕は名前を知らずに、問題から辿り着いたんです」
-->

---

<!-- _class: big -->

# 問題が、設計を導いた

AIが正しい答えをくれたわけじゃない

問題を持っていたから、設計に辿り着いた

<!--
「AIが正しい答えをくれたわけじゃありません。問題を持っていたから、設計に辿り着けたんです」
はっきり。「問題が、設計を導いた」★2回目
-->

---

<!-- _class: big -->

# 問題を持て、AIに聞け、自分で作れ

### 今のAIならもっとできる

ありがとうございました

<!--
「皆さんも問題を持ってください。AIに聞いてください。自分で作ってください。今のAIならもっとできます」
「ありがとうございました」
-->
