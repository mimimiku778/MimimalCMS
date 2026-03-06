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
  section.code-shame pre {
    background: #fff0f0;
  }
  section.code-shame h3 {
    color: #c0392b;
  }
---

<!-- HTML生成: npx @marp-team/marp-cli --no-stdin docs-9/slides.md --html -o docs-9/slides.html -->

<!-- _class: title -->

# 「フレームワークを作れば<br>開発力が上がる」で殴り抜けた話

<br>

## pika

<!--
「pikaです。独学でPHPフレームワークを自作した話をします。……かなり恥ずかしいコードが出てきますが、温かい目でお願いします」
（笑いを待つ）
-->

---

## Progateから独学で始めて、一応は作れるようになった

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
「Progateから始めて、掲示板が作れるようになりました。OAuth 2.0とか画像処理とか、機能は作れるんです」
「ただ、ちょっとそのときのコードを見てください」
-->

---

<!-- _class: code-shame -->

## 当時の傑作をご覧ください

```
Api.difine.php   ← はい、defineのスペルミスです
```

定数 **61個**、フラットに並んでます

### 名前空間？ 知らない子ですね

<!--
「まずこちら。Api.difine.phpです」
（間を取る）
「……はい、defineのスペルミスです」
（笑いを待つ）
「中身は定数61個がフラットに並んでいます。名前空間？ 知らない子ですね」
-->

---

## コピペの神に愛された男

```php
// GetReview.php
$id = (int)filter_input(INPUT_GET, 'id');

// GetRanking.php
$id = (int)filter_input(INPUT_GET, 'id');

// GetUser.php
$id = (int)filter_input(INPUT_GET, 'id');
```

GETパラメータの取得コード、**3クラスに同じもの**が書いてある

<!--
「次にこちら。GETパラメータを整数で取得するコードです」
「3つのクラスに全く同じものが書いてあります」
（間）
「コピペの何が悪いんですか、動くんですよ？」
（笑いを待つ）
「……いや、悪いんですけど」
-->

---

## 継承の深淵を覗いた者

```
LoginController
  → Author
    → AuthorAbstract
      → Cookie
      → LineAuthDB
```

5層の継承。**何もしていない層がある**

<!--
「そして極めつけがこちらです。LoginControllerの継承ツリー」
「5層あります。でも途中の層、何もしていないんです」
（間を取って）
「一体何を守っていたんでしょうね、このAbstractは」
（笑いを待つ）
-->

---

<!-- _class: big -->

## どこに何を書けばいいか分からない

### 何度も最初からやり直した

<!--
「笑い話みたいですけど、当時は本当に辛かったんです」
（トーンを変える。真面目に）
「どこに何を書けばいいか、分からなかった。何度も最初からやり直しました」
★ここからは真面目モード
-->

---

<!-- _class: mid -->

## QiitaでLaravelの記事を読んだ

「こう書くとこう動く」は分かった

### なぜそう書くのかは分からなかった

ちょうどその頃、ChatGPT（GPT-3.5）が出た

ハルシネーションだらけだったが、壁打ち相手にはなった

<!--
「QiitaでLaravelの記事を読みました。こう書くとこう動くのは分かるんですが、なぜそう書くのかが分からない」
「ちょうどGPT-3.5が出まして。ハルシネーションだらけでしたけど壁打ち相手にはなりました」
「……difineと書く人間にはちょうどいい精度だったかもしれません」
（軽く笑いを取る。さくさく進む）
-->

---

<!-- _class: title -->

## アプリケーションの処理自体は書ける
## どう整理すればいいかが分からなかった

### Laravelの使い方だけ見て、裏側を自分で作ることにした

<!--
「処理自体は書けるんです。61個の定数もOAuth 2.0も、動くものは作れる」
「でもそれをどう整理すればいいかが分からなかった」
「それでLaravelの使い方だけ見て、裏側の仕組みを自分で作ることにしました」
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
「まず一番困っていたのは、どこに何を書くか。URLとクラス名を対応させて、ファイルを置くだけでルーティングされるようにしました」
「設定ファイルは0行です。61個の定数を書いていた人間の反動です」
（笑いを待つ）
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
「引数の型を自動で読み取る方法はないかとChatGPTに聞いたら、リフレクションというものがあると。これで何でもできるなと思いました」
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
「クラスが大きくなりすぎたので分割したいと思ったとき、再帰にすればいいだけだと気づきました」
「1行変えただけで全部自動で解決されました」←力入れる
ぼそっと「問題が、仕組みを教えてくれた」★1回目
「あの5層の継承ピラミッドからは想像もつかない進歩です」
（軽く笑い）
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
「difineファイルに61個定数を書いていた人間が作ったとは思えないでしょう？」
（笑いを待つ。一拍）
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

## 自分が作ったもの、全部名前がついていた

「どこに何を書くか」の答え → 「設定より規約」と呼ばれていた
Convention over Configuration

newを手で書いて繋いでいた問題の答え → 「依存性の注入」と呼ばれていた
Dependency Injection

<!--
「後から知ったんですが、自分が作ったもの、全部名前がついていました」
「どこに何を書くかの答えは、設定より規約」
「newを手で繋いでいた問題の答えは、依存性の注入」
「先に名前を知っていたら、こんな遠回りはしなかったかもしれません。でも遠回りしたから、本当に理解できたんだと思います」
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
「difineのスペルミスから始めた人間でも、ここまで来れました」
（笑いを待つ）
「ありがとうございました」
-->
