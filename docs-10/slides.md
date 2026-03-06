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
  section.contrast {
    font-size: 36px;
  }
  section.contrast table {
    width: 100%;
    font-size: 0.9em;
  }
  section.contrast th {
    background: #f0f0f0;
  }
---

<!-- HTML生成: npx @marp-team/marp-cli --no-stdin docs-10/slides.md --html -o docs-10/slides.html -->

<!-- _class: title -->

# 「フレームワークを作れば<br>開発力が上がる」で殴り抜けた話

<br>

## pika

<!--
「pikaです。独学でPHPフレームワークを自作した話をします。AIとどう学ぶか、という話でもあります」
-->

---

<!-- _class: big -->

## 2023年、GPT-3.5が来た

### あなたはそのとき、AIとどう向き合いましたか？

<!--
「2023年、GPT-3.5が来ました。皆さんはそのとき、AIとどう向き合いましたか？」
（間を取る。問いかけを意識させる）
-->

---

## 僕はProgateから独学で始めた初心者だった

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

機能は作れる。でもどこに何を書けばいいか分からない

<!--
「僕はProgateから始めた独学の初心者でした。機能は作れるんです。OAuth 2.0とか画像処理とか。でもどこに何を書けばいいか分からなかった」
-->

---

<!-- _class: mid -->

## QiitaでLaravelの記事を読んだ

「こう書くとこう動く」は分かった

### なぜそう書くのかは分からなかった

<!--
「QiitaでLaravelの記事を読みました。こう書くとこう動くのは分かる。でもなぜそう書くのかが分からない」
「ドキュメントを読むだけでは、設計の意味は分からなかったんです」
-->

---

<!-- _class: contrast -->

## AIに「答え」を聞いても学べなかった

| やったこと | 結果 |
|---|---|
| 「Laravelの使い方を教えて」 | コードは出てくる。理解は増えない |
| 「DIコンテナとは何ですか」 | 説明は出てくる。なぜ必要か分からない |
| 「設計パターンを教えて」 | 名前は覚えた。使いどころが分からない |

**問題を持たずにAIに聞いても、知識が積み上がらなかった**

<!--
「GPT-3.5に色々聞きました。でも答えを聞いても学べなかったんです」
「DIコンテナとは何ですか、と聞けば説明は出てくる。でもなぜそれが必要なのか、自分の問題と結びつかない」
「問題を持たずにAIに聞いても、知識が積み上がらなかった」
-->

---

<!-- _class: title -->

## そこでアプローチを変えた

### Laravelの使い方だけ見て、裏側を自分で作ることにした

### AIには「答え」ではなく「手段」を聞く

<!--
「そこでアプローチを変えました。Laravelの使い方だけ見て、裏側を自分で作る。AIには答えではなく、手段を聞くことにしました」
-->

---

## 問題：「どこに何を書くか」が分からない

```
app/Controllers/Pages/
    ImagePageController.php   ← /image でアクセスされる
    IndexPageController.php   ← / でアクセスされる
```

URLとクラス名を対応させた。設定ファイルは0行

<!--
「まず一番の問題。どこに何を書くか。URLとクラス名を対応させて、ファイルを置くだけでルーティングされるようにしました。設定ファイルは0行です」
-->

---

## AIへの聞き方が変わった

```
× 「ルーティングの作り方を教えて」
  → 一般的な説明が返ってくるだけ

○ 「PHPで引数の型情報を実行時に取得する方法はある？」
  → 「リフレクションがあります」
```

**問題を持っていたから、具体的に聞けた**

<!--
「ここでAIへの聞き方が変わったんです。ルーティングの作り方を教えて、ではなく、PHPで引数の型情報を実行時に取得する方法はある？と聞いた」
「問題を持っていたから、具体的に聞けた。具体的に聞いたから、使える答えが返ってきた」
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
「リフレクションで引数の型を読んで、自動でnewして渡す。AIが教えてくれたのはリフレクションという手段だけ。それを使って何を作るかは、自分の問題が決めました」
-->

---

## 問題がさらに進化した

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
「クラスが大きくなりすぎたので分割したいという新しい問題が生まれました。そこで再帰にすればいいだけだと気づいた。1行変えただけで全部自動で解決されました」
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
止まる。読ませる。
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

## 実サービスで今も動いている

コントローラ 41個 / モデル 145個 / サービス 143個

コミット 3,300+ / PR 233本

今もユーザーがいる実サービス

<!--
「このフレームワークで実際にWebサービスを作って公開しました。今も動いています」
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
「AIに設計パターンを教えてと聞いても分からなかった。でも自分で問題を解いたら、同じ場所に辿り着いていた」
-->

---

<!-- _class: contrast -->

## AI時代の学び方

| 受動的 | 能動的 |
|---|---|
| 「設計パターンを教えて」 | 「自分のコードのこの問題を解きたい」 |
| AIが答えをくれる | AIが手段をくれる |
| 知識は増える | **理解が積み上がる** |

<!--
「AI時代の学び方には2種類あると思います。答えを聞く受動的な使い方と、問題を持って手段を聞く能動的な使い方」
「知識が増えるのと、理解が積み上がるのは違います」
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
「2023年のGPT-3.5でここまで来れました。今のAIならもっとできます」
「でも変わらないことがある。問題を持ってください。AIに聞いてください。自分で作ってください」
「ありがとうございました」
-->
