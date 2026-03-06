---
marp: true
theme: default
paginate: true
style: |
  section {
    font-family: 'Noto Sans JP', 'Hiragino Kaku Gothic ProN', sans-serif;
    font-size: 28px;
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
    font-size: 36px;
  }
  section code {
    font-size: 0.85em;
  }
  section.compare {
    font-size: 24px;
  }
---

<!-- _class: title -->

# 「フレームワークを作れば<br>開発力が上がる」で殴り抜けた話

<br>

## pika

<!--
「pikaです。独学でPHPフレームワークを自作した話をします。コード多めでいきます」
-->

---

## 独学で書いていた頃のコード

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

コンストラクタ＝プログラムそのもの。newした瞬間にすべてが走る

<!--
「Progateから始めて、こういうコードを書いていました。コンストラクタがプログラムそのものです。newした瞬間にすべてが走ります」
-->

---

<!-- _class: compare -->

## 実際の構造：定数61個がフラットに並ぶ

```php
// Api.difine.php（実際のファイル名。typoもそのまま）
define('DB_NAME', '...');
define('DB_HOST', '...');
define('LINE_CLIENT_ID', '...');
define('LINE_CLIENT_SECRET', '...');
define('REVIEW_MAX_LENGTH', 500);
// ...全部で61個。グルーピングなし、名前空間なし
```

設定も認証情報も上限値も、全部同じファイルに

<!--
「設定ファイルはこれです。Api.difine.phpというファイルに定数が61個フラットに並んでいます。ファイル名のtypoもそのままです。DBの接続情報もLINEの認証情報もバリデーションの上限も、全部同じファイルにdefineで書いていました」
-->

---

<!-- _class: compare -->

## 同じ処理が3クラスに存在していた

```php
// GETParamInt（クラスA）
class GetParamOptional {
    function __construct(string $name) {
        $this->value = (int)filter_input(INPUT_GET, $name);
    }
}
// GETParamInt（クラスB）── ほぼ同じコード
// GETParamInt（クラスC）── ほぼ同じコード
```

コピペで増殖。どれが正しいか分からない

<!--
「GETパラメータを整数で取得する処理が、3つのクラスに重複していました。コピペで増えていって、どれが本物か分からない状態です」
-->

---

## 5層の無意味なラッピング

```
LoginController
  → Author
    → AuthorAbstract
      → Cookie
      → LineAuthDB
```

分けてはいる。でも分ける理由がない

<!--
「ログイン処理はこうなっていました。5層です。分けてはいるんです。Abstractも使っている。でも、分ける理由がない。教科書で見たパターンを形だけ真似していました」
-->

---

<!-- _class: mid -->

## 作れるのに、整理できない

QiitaでLaravelの記事を読んだ

「こう書くとこう動く」は分かった。なぜそう書くかは分からなかった

GPT-3.5に壁打ちしながら、裏側を自分で作ることにした

<!--
「作れるのに整理できない。それが当時の状態でした。Laravelの記事を読んでも、なぜそう書くかが分からない。GPT-3.5に壁打ちしながら、裏側を自分で作ることにしました」
-->

---

## 解決策1: ファイルを置くだけでルーティング

```php
// shadow/Kernel/RewriteRouter.php（実際のフレームワークコード）
// URLからクラス名を自動解決
// /image → app/Controllers/Pages/ImagePageController
// /api/review → app/Controllers/Api/ReviewApiController
```

```
app/Controllers/Pages/
    ImagePageController.php   ← /image
    IndexPageController.php   ← /
```

設定ファイル0行。URLとディレクトリ構造が一致する

<!--
「まずルーティングです。URLとクラス名を対応させて、ファイルを置くだけで動くようにしました。設定ファイルは0行です」
-->

---

## 解決策2: リフレクションでDI

```php
// 使う側
class ImagePageController {
    public function __construct(ImageStore $store) {
        // 型を書くだけで自動注入
    }
}

// フレームワーク側（実際のコード概要）
$params = (new ReflectionMethod($class, '__construct'))->getParameters();
foreach ($params as $param) {
    $type = $param->getType()->getName();
    $args[] = new $type;  // 型名でnew
}
```

<!--
「次にDIです。リフレクションで引数の型を読み取って、自動でnewして渡します。フレームワーク側の実装はこれだけです」
-->

---

## 転機: 再帰で依存を解決する

```php
// Before: 1階層だけ
$args[] = new $type;

// After: 再帰で無限階層
function resolve(string $class): object {
    $params = (new ReflectionClass($class))
        ->getConstructor()->getParameters();
    $args = array_map(fn($p) =>
        resolve($p->getType()->getName()), $params);
    return new $class(...$args);
}
```

```
ImageStore → Database → Config  // 全部自動でnewされる
```

**1行の変更で、依存解決が完成した**

<!--
「クラスが大きくなって分割したくなったとき、依存の先の依存も自動で解決できたらいいなと思いました。再帰にすればいいだけでした。これが実質的なDIコンテナの完成です」
「問題が、仕組みを教えてくれた」ぼそっと
-->

---

## バリデーション: 宣言的に書く

```php
Route::path('image/store@post')
    ->matchFile('file', ['image/jpeg'])
    ->matchStr('imageType', regex: '/.+/')
    ->matchNum('imageSize', max: 1000);
```

ルーティングとバリデーションが一体化。エラー処理も自動

<!--
「バリデーションはルーティングと一体化させました。宣言的に書くだけで、エラー処理も自動です」
さっと
-->

---

## Before → After

```php
// Before: コンストラクタ=プログラム全体
class PostReview extends ApiAbstract {
    public function __construct() {
        $this->init($this->validation(), ...);
        $insert_db = new InsertDB($this->sql);
        // 認証もDBも全部ここに...
    }
}

// After: やりたいことだけ
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
「Before/Afterを並べます。上がフレームワークを作る前。下が作った後。設定ファイルなし、newなし。やりたいことだけ書けばいい状態になりました」
-->

---

<!-- _class: title -->

# できた

MVC / ルーティング / DIコンテナ / バリデーション
CSRF対策 / 自動XSSエスケープ / ヘルパー関数

外部ライブラリ依存ゼロ

<!--
「で、できました。外部依存ゼロです」
止まる
-->

---

## 実サービスで検証した

コントローラ 41個 / モデル 145個 / サービス 143個

コミット 3,300+ / PR 233本

今もユーザーがいる実サービスで動いている

<!--
「このフレームワークで実際にWebサービスを作って公開しました。今も動いています」
-->

---

## 自分が作ったもの、全部名前がついていた

| 自分の課題 | 作ったもの | 既存の名前 |
|---|---|---|
| どこに何を書くか | URLとクラス名の対応 | Convention over Configuration |
| 手でnewし続ける苦痛 | リフレクションで自動注入 | Dependency Injection |
| 依存の先の依存 | 再帰で自動解決 | DIコンテナ |

<!--
「後から知ったんですが、自分が作ったもの全部名前がついていました。設定より規約、依存性の注入、DIコンテナ。全部、問題から辿り着いた設計でした」
-->

---

<!-- _class: big -->

# 問題が、設計を導いた

AIが正しい答えをくれたわけじゃない

問題を持っていたから、設計に辿り着いた

<!--
「AIが正しい答えをくれたわけじゃありません。問題を持っていたから、設計に辿り着けたんです」
はっきり。「問題が、設計を導いた」
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
