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
  section.lesson {
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: center;
    font-size: 1.5em;
  }
  section.lesson h2 {
    color: #2563eb;
  }
---

<!-- HTML生成: npx @marp-team/marp-cli --no-stdin docs-8/slides.md --html -o docs-8/slides.html -->

<!-- _class: title -->

# 「フレームワークを作れば<br>開発力が上がる」で殴り抜けた話

<br>

## pika

<!--
「pikaです。独学でPHPフレームワークを自作した話をします。今日は自分が得た学びを5つ、順番にお話しします」
-->

---

<!-- _class: mid -->

## Progateから独学で始めた

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

<!-- _class: lesson -->

## 学び 1
## 「書ける」と「整理できる」は別の能力

<!--
「ここで最初の学びです。書けると整理できるは、別の能力でした。コードは書ける。でもどう整理するかは、書けるだけでは分からなかった」
一拍
-->

---

<!-- _class: mid -->

## QiitaでLaravelの記事を読んだ

「こう書くとこう動く」は分かった

### なぜそう書くのかは分からなかった

ちょうどその頃、ChatGPT（GPT-3.5）が出た

ハルシネーションだらけだったが、壁打ち相手にはなった

<!--
「QiitaでLaravelの記事を読みました。こう書くとこう動くのは分かるけど、なぜそう書くのかが分からなかった。ちょうどGPT-3.5が出て、壁打ち相手にはなりました」
-->

---

<!-- _class: lesson -->

## 学び 2
## 使い方ではなく「なぜ」を追え

### Laravelの使い方だけ見て、裏側を自分で作ることにした

<!--
「学び2つ目。使い方ではなく、なぜを追え。それでLaravelの使い方だけ見て、裏側の仕組みを自分で作ることにしました」
-->

---

## まず「どこに何を書くか」を解決した

```
app/Controllers/Pages/
    ImagePageController.php   ← /image でアクセスされる
    IndexPageController.php   ← / でアクセスされる
```

ファイルを置くだけ。設定ファイルは0行

<!--
「まずURLとクラス名を対応させて、ファイルを置くだけでルーティングされるようにしました。設定ファイルは0行です」
-->

---

## 次に「newを手で書く問題」を解決した

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
「次にリフレクションで引数の型を読んで、自動でnewするようにしました」
さっと
-->

---

<!-- _class: lesson -->

## 学び 3
## 困っていることを1つずつ潰せば、仕組みになる

<!--
「学び3つ目。困っていることを1つずつ潰していけば、それが仕組みになります。大きな設計を考える必要はなかった。目の前の不便を解消しただけです」
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

<!-- _class: lesson -->

## 学び 4
## 問題が、設計を導く

### AIが正しい答えをくれたわけじゃない
### 問題を持っていたから、設計に辿り着いた

<!--
「学び4つ目。問題が、設計を導く。AIが正しい答えをくれたわけじゃありません。問題を持っていたから、設計に辿り着けたんです」
はっきり。★2回目
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
Route::path('image/store@post')
    ->matchFile('file', ['image/jpeg'])
    ->matchNum('imageSize', max: 1000);
```

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

<!--
「全部合わさるとこうなります。ルーティング、バリデーション、DI。やりたいことだけ書けばいい」
-->

---

## このフレームワークで実サービスを作って公開した

コントローラ 41個 / モデル 145個 / サービス 143個

コミット 3,300+ / PR 233本

今もユーザーがいる実サービス

<!--
「このフレームワークで実サービスを作って公開しました。今も動いています」
-->

---

## 後から知った：自分が作ったもの、全部名前がついていた

「どこに何を書くか」の答え → 「設定より規約」と呼ばれていた
Convention over Configuration

newを手で書いて繋いでいた問題の答え → 「依存性の注入」と呼ばれていた
Dependency Injection

<!--
「後から知ったんですが、自分が作ったもの、全部名前がついていました。設定より規約、依存性の注入」
淡々と
-->

---

<!-- _class: lesson -->

## 学び 5
## 問題を持て、AIに聞け、自分で作れ

### 名前は後から知ればいい
### 今のAIならもっとできる

<!--
「最後の学び。問題を持ってください。AIに聞いてください。自分で作ってください。名前は後から知ればいい。今のAIならもっとできます」
-->

---

<!-- _class: big -->

# 問題が、設計を導いた

ありがとうございました

<!--
「問題が、設計を導いた。ありがとうございました」
-->
