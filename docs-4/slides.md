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
    font-size: 2.8em;
  }
  section.big h3 {
    font-size: 0.5em;
  }
  section.mega {
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: center;
    font-size: 3.5em;
  }
  section.mid {
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: center;
    font-size: 2em;
  }
  section.code {
    font-size: 28px;
  }
---

<!-- _class: title -->

# 「フレームワークを作れば<br>開発力が上がる」で殴り抜けた話

<br>

## pika

<!--
「pikaです。独学でPHPフレームワークを自作した話をします」
-->

---

<!-- _class: mega -->

Progateから始めた

<!--
「Progateから始めました」
2秒待つ
-->

---

<!-- _class: mega -->

掲示板が作れた

<!--
「掲示板が作れるようになりました。OAuth認証も、画像処理も、一応書けるようになりました」
-->

---

<!-- _class: mega -->

整理できなかった

<!--
「でも、整理できなかったんです」
3秒黙る
-->

---

<!-- _class: big -->

## どこに何を書けばいいか
## 分からない

<!--
「どこに何を書けばいいか、分からなかったんです」
長く黙る。5秒。読ませる。
「何度も最初からやり直しました」
-->

---

<!-- _class: mid -->

Laravelの記事を読んだ

「なぜそう書くか」が分からなかった

<!--
「QiitaでLaravelの記事を読みました。こう書くとこう動くのは分かる。でもなぜそう書くかが分からなかった」
-->

---

<!-- _class: mid -->

GPT-3.5が出た

壁打ち相手にした

<!--
「ちょうどGPT-3.5が出ました。ハルシネーションだらけでしたが、壁打ち相手にはなりました」
さくさく
-->

---

<!-- _class: big -->

## 裏側を自分で作る

<!--
「Laravelの使い方だけ見て、裏側の仕組みを自分で作ることにしました」
-->

---

<!-- _class: code -->

## ファイルを置くだけ

```
app/Controllers/Pages/
    ImagePageController.php   ← /image
    IndexPageController.php   ← /
```

設定ファイル 0行

<!--
「まずルーティング。URLとクラス名を対応させて、ファイルを置くだけ。設定ファイルは0行です」
-->

---

<!-- _class: code -->

## 型を書くだけ

```php
class ImagePageController {
    public function __construct(ImageStore $store) {
        // 型を見て自動で new して渡す
    }
}
```

<!--
「次に、型を書くだけで自動で注入されるようにしました。リフレクションで引数の型を読んでnewします」
-->

---

<!-- _class: code -->

## 再帰にしただけ

```php
// ImageStore → Database → Config
// 全部自動でnewされる
```

1行変えただけだった

<!--
「クラスを分割したくなって、依存の先も自動で解決できないかと思ったんです。再帰にすればいいだけでした。1行変えただけです」
力を込める
-->

---

<!-- _class: big -->

## 問題が、仕組みを教えてくれた

<!--
ぼそっと「問題が、仕組みを教えてくれた」
5秒黙る。★1回目
-->

---

<!-- _class: mega -->

できた

<!--
「できました」
3秒黙る
-->

---

<!-- _class: mid -->

MVC / ルーティング / DIコンテナ
バリデーション / CSRF対策
自動XSSエスケープ

外部ライブラリ依存ゼロ

<!--
読ませる。口で言わない。3秒待つ
-->

---

<!-- _class: mid -->

実サービスで動いている

コントローラ41 / モデル145 / サービス143

コミット3,300+ / PR 233

<!--
「このフレームワークで実際にWebサービスを作りました。今も動いています」
-->

---

<!-- _class: big -->

## 後から知った

<!--
「後から知ったんですが」
長く黙る
-->

---

<!-- _class: mid -->

全部名前がついていた

Convention over Configuration
Dependency Injection

<!--
「自分が作ったもの、全部名前がついていました。設定より規約。依存性の注入」
淡々と
-->

---

<!-- _class: big -->

## 問題が、設計を導いた

### AIが答えをくれたんじゃない
### 問題を持っていたから辿り着いた

<!--
「AIが正しい答えをくれたわけじゃありません。問題を持っていたから、設計に辿り着けたんです」
はっきり。★2回目
-->

---

<!-- _class: big -->

## 問題を持て
## AIに聞け
## 自分で作れ

### ありがとうございました

<!--
「問題を持ってください。AIに聞いてください。自分で作ってください。今のAIならもっとできます」
「ありがとうございました」
-->
