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
  section.dark {
    background: #1a1a2e;
    color: #eee;
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }
  section.dark h2 {
    color: #e94560;
  }
  section.dark h3 {
    color: #aaa;
  }
---

<!-- _class: title -->

# 「フレームワークを作れば<br>開発力が上がる」で殴り抜けた話

<br>

## pika

<!--
「pikaです。今日は、独学でPHPフレームワークを自作した話をします」
深呼吸
「……正直に言うと、これは苦しかった頃の話です」
-->

---

<!-- _class: big -->

## 2022年8月

### Progateを開いた日

<!--
「2022年の8月。Progateを開きました」
黙る。3秒。
「プログラミングというものを、ゼロから始めました」
-->

---

## 掲示板が作れるようになった

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

newするだけで全部動く。動くことは動く。

<!--
「掲示板が作れるようになりました。OAuth認証も、画像処理も、APIも作れました」
一拍
「動くことは動くんです」
-->

---

<!-- _class: dark -->

## 暗黒時代

### 機能を足すたびにコードが絡まる
### 直そうとするともっと絡まる

<!--
長めに黙る。4秒。
「でも、機能を足すたびにコードが絡まっていきました」
「直そうとすると……もっと絡まる」
声を落とす
-->

---

<!-- _class: dark -->

## どこに何を書けばいいか<br>分からない

### 何度も、何度も最初からやり直した

<!--
「どこに何を書けばいいか、分からなかったんです」
5秒黙る。スライドを読ませる。
「何度も……何度も最初からやり直しました」
★ここが一番苦しかった場所。急がない
-->

---

<!-- _class: mid -->

## Laravelの記事を読んでみた

「こう書くとこう動く」は分かった

### でも、なぜそう書くのかは分からなかった

<!--
「QiitaでLaravelの記事を読みました」
「こう書くとこう動くのは分かるんです」
一拍
「でも、なぜそう書くのか。それが分からなかった」
-->

---

<!-- _class: mid -->

## ちょうどその頃、GPT-3.5が出た

ハルシネーションだらけだった

### でも壁打ち相手にはなった

<!--
「ちょうどその頃GPT-3.5が出ました。ハルシネーションだらけでしたけど、壁打ち相手にはなりました」
さくさく
-->

---

<!-- _class: title -->

## 処理は書ける。整理ができない。

### ……だったら、整理する側を自分で作ればいい

<!--
「処理は書けるんです。整理ができない」
3秒。
「だったら……整理する側を、自分で作ればいいんじゃないか」
ここ、声に力を入れる
-->

---

## 一番の悩み：「どこに何を書くか」

```
app/Controllers/Pages/
    ImagePageController.php   ← /image でアクセスされる
    IndexPageController.php   ← / でアクセスされる
```

ファイルを置くだけ。設定ファイルは0行

<!--
「一番の悩みは、どこに何を書くかでした。URLとクラス名を対応させて、ファイルを置くだけでルーティングされるようにしました」
-->

---

## 次の悩み：手でnewし続ける苦痛

Laravelのコントローラ、引数にクラスが並んでいる

手でnewしているわけじゃないらしい

**便利だな。作りたい。**

<!--
「次に苦しかったのは、手でnewし続けることでした。Laravelのコントローラを見たら、引数にクラスが並んでいて、自動で渡されているらしい」
「便利だな。作りたい。そう思いました」
-->

---

## リフレクションを知った

```php
class ImagePageController {
    public function __construct(ImageStore $store) {
        // ↑ 型を見て自動で new して渡す
    }
}
// 裏側: リフレクションで引数の型を読んで new する
```

<!--
「ChatGPTに聞いたら、リフレクションというものがあると教えてくれました。引数の型を実行時に読み取れる」
さっと
-->

---

## そして、あの瞬間

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

**1行変えただけだった**

<!--
「クラスが大きくなりすぎて分割したかったんです。依存の先の依存も自動で解決できたらいいのに、と」
3秒黙る
「再帰にすればいいだけだと気づきました。1行変えただけで、全部自動で解決されました」
声に力を込める
-->

---

<!-- _class: big -->

## 問題が、仕組みを教えてくれた

<!--
ぼそっと「問題が、仕組みを教えてくれた」
黙る。5秒。★1回目
-->

---

<!-- _class: title -->

# できた

MVC / ルーティング / DIコンテナ / バリデーション
CSRF対策 / 自動XSSエスケープ / ヘルパー関数

外部ライブラリ依存ゼロ

<!--
「……できました」
長めに止まる。読ませる。
-->

---

## このフレームワークでWebサービスを公開した

コントローラ 41個 / モデル 145個 / サービス 143個

コミット 3,300+ / PR 233本

**今もユーザーがいる実サービス**

<!--
「このフレームワークで実際にWebサービスを作って公開しました。今も動いています」
数字はスライドに任せる
-->

---

<!-- _class: big -->

## 後から知った

<!--
「……後から知ったんですが」
長く黙る。6秒。
-->

---

## 全部、名前がついていた

「どこに何を書くか」の答え → **Convention over Configuration**（設定より規約）

newを手で書いて繋いでいた問題の答え → **Dependency Injection**（依存性の注入）

<!--
「自分が作ったもの、全部名前がついていました」
「どこに何を書くかの答えは、設定より規約と呼ばれていました」
「newを手で繋いでいた問題の答えは、依存性の注入と呼ばれていました」
淡々と。でも感慨を込めて
-->

---

<!-- _class: big -->

# 問題が、設計を導いた

AIが正しい答えをくれたわけじゃない

問題を持っていたから、設計に辿り着いた

<!--
「AIが正しい答えをくれたわけじゃありません」
一拍
「問題を持っていたから、設計に辿り着けたんです」
はっきり。「問題が、設計を導いた」★2回目
-->

---

<!-- _class: big -->

# 問題を持て、AIに聞け、自分で作れ

### 今のAIならもっとできる

ありがとうございました

<!--
「問題を持ってください。AIに聞いてください。自分で作ってください」
「今のAIならもっとできます」
「ありがとうございました」
-->
