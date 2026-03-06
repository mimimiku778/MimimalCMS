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
    font-size: 28px;
  }
  section.contrast h2 {
    font-size: 1.3em;
    margin-bottom: 0.3em;
  }
  table {
    width: 100%;
  }
  th {
    font-size: 0.9em;
    color: #666;
  }
---

<!-- HTML生成: npx @marp-team/marp-cli --no-stdin docs-7/slides.md --html -o docs-7/slides.html -->

<!-- _class: title -->

# 「フレームワークを作れば<br>開発力が上がる」で殴り抜けた話

<br>

## pika

<!--
「pikaです。今日はビフォーアフターでお見せします。独学でPHPフレームワークを自作した話です」
-->

---

<!-- _class: big -->

## Before → After

### 同じ人間が書いたコードです

<!--
「これから見せるのは、全部同じ人間が書いたコードです。Beforeが2023年2月、Afterが2023年4月。たった2ヶ月の差です」
-->

---

<!-- _class: contrast -->

## Before: コンストラクタ＝プログラム全体

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
どこに何を書けばいいか分からない

<!--
「Beforeです。コンストラクタの中にプログラム全体が入っている。newするだけで全部走る。どこに何を書けばいいか分からなかった」
-->

---

<!-- _class: contrast -->

## After: やりたいことだけ書く

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
「Afterです。やりたいことだけ書く。設定ファイルなし、newなし。同じ人間が2ヶ月後に書いたコードです」
一拍
-->

---

<!-- _class: big -->

## 何が変わったのか？

<!--
「何が変わったのか。1つずつ見せます」
-->

---

<!-- _class: mid -->

## Progateから独学で始めた

「こう書くとこう動く」は分かった

### なぜそう書くのかは分からなかった

ChatGPT（GPT-3.5）が出て、壁打ち相手にはなった

Laravelの使い方だけ見て、裏側を自分で作ることにした

<!--
「Progateから独学で始めました。Laravelの記事を読んでも、なぜそう書くのかが分からなかった。ちょうどGPT-3.5が出て壁打ち相手にはなりました。それで裏側を自分で作ることにしました」
さくさく
-->

---

## 対比 1: ルーティング

**手動: どのファイルが何のURLか分からない**

```
api_post_review.php / api_get_ranking.php / ...
```

**自動: ファイルを置くだけ。設定0行**

```
app/Controllers/Pages/
    ImagePageController.php   ← /image でアクセスされる
    IndexPageController.php   ← / でアクセスされる
```

<!--
「対比その1、ルーティング。手動ではどのファイルが何のURLか分からなかった。自動にしたらファイルを置くだけ。設定ファイルは0行です」
-->

---

## 対比 2: 依存の組み立て

**手動: 全部自分でnewする**

```php
$config = new Config();
$db = new Database($config);
$store = new ImageStore($db);
```

**自動: 型を書くだけ**

```php
public function __construct(ImageStore $store) {
    // ↑ 型を見て自動で new して渡す
}
```

<!--
「対比その2、依存の組み立て。手動では全部自分でnewしていた。自動にしたら型を書くだけです」
-->

---

## 対比 3: 依存の連鎖

**手動: 3つのクラスをnewする順番を間違えたらエラー**

**自動: 再帰で全部解決**

```php
class ImageStore {
    public function __construct(Database $db) { ... }
}
class Database {
    public function __construct(Config $config) { ... }
}
// ImageStore → Database → Config 全部自動でnewされる
```

1行変えただけだった

<!--
「対比その3。クラスが大きくなりすぎて分割したいと思った。再帰にすればいいだけだと気づいた。1行変えただけでした」
-->

---

<!-- _class: title -->

**問題が、仕組みを教えてくれた**

手動で困ったから、自動にする方法を考えた
自動にしたら、それが設計パターンだった

<!--
ぼそっと「問題が、仕組みを教えてくれた」
「手動で困ったから自動にする方法を考えた。自動にしたら、それが設計パターンだった」
★1回目
-->

---

## 対比 4: バリデーション

**手動: ifの羅列**

```php
if (!isset($_POST['file'])) { httpResponse(400); }
if ($_POST['size'] > 1000) { httpResponse(400); }
```

**自動: 宣言するだけ**

```php
Route::path('image/store@post')
    ->matchFile('file', ['image/jpeg'])
    ->matchNum('imageSize', max: 1000);
```

<!--
「対比その4、バリデーション。ifの羅列が宣言するだけになった」
さっと
-->

---

<!-- _class: title -->

## できたもの

MVC / ルーティング / DIコンテナ / バリデーション
CSRF対策 / 自動XSSエスケープ / ヘルパー関数

外部ライブラリ依存ゼロ

<!--
「できました。外部依存ゼロです」
止まる。読ませる。
-->

---

## 最大の対比: Before → After

| | Before | After |
|---|---|---|
| 始めてから | 6ヶ月 | 8ヶ月 |
| コード | PHPファイル43個 | フレームワーク + 実サービス |
| 設計 | なし | MVC, DI, CoC |
| サービス | なし | コミット3,300+, PR 233本 |

<!--
「最大の対比です。始めて6ヶ月のコードと、8ヶ月後のコード。たった2ヶ月で変わりました」
-->

---

<!-- _class: big -->

## 後から知ったんですが

<!--
「……後から知ったんですが」
黙る。
-->

---

## 自分が作ったもの、全部名前がついていた

「どこに何を書くか」の答え → 「設定より規約」
Convention over Configuration

newを手で書いて繋いでいた問題の答え → 「依存性の注入」
Dependency Injection

<!--
「自分が作ったもの、全部名前がついていました」
淡々と
-->

---

<!-- _class: big -->

# 問題が、設計を導いた

AIが正しい答えをくれたわけじゃない

問題を持っていたから、設計に辿り着いた

<!--
「AIが正しい答えをくれたわけじゃありません。問題を持っていたから、設計に辿り着けたんです」
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
