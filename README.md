# MimimalCMS

超軽量でシンプルなPHP MVCフレームワーク

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE.md)
[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue.svg)](https://www.php.net/)

**言語:** [日本語](README.md) | [English](README_EN.md)

## 概要

MimimalCMSは、最小限のコードで最大限の柔軟性を実現する超軽量PHPフレームワークです。MVCアーキテクチャをベースに、規約ベースのルーティング、依存性注入、セキュリティ機能など、Webアプリケーション開発に必要な基本機能を提供します。

### 主な特徴

- 🚀 **超シンプルなルーティング** - ファイル作成だけでページ追加完了
- 🎯 **柔軟なルーティング定義** - パスパラメータ、複数HTTPメソッド、カスタムバリデーション対応
- 🔒 **セキュリティ機能** - CSRF対策、XSS対策、型安全なパラメータバリデーション
- 📦 **依存性注入コンテナ** - 自動コンストラクタインジェクション、インターフェースバインディング
- 🛠️ **RESTful API対応** - 規約ベースで自動的にAPI/Pageコントローラーを振り分け
- 🖼️ **画像処理機能** - 安全な画像検証・リサイズ・保存
- 📝 **シンプルなテンプレートエンジン** - PHPベースで自動XSSエスケープ

## このフレームワークで構築されたプロダクト

**オプチャグラフ (OpenChat Graph)** - LINE OpenChatのメンバー数推移を可視化するWebサービス
🔗 https://openchat-review.me
📦 GitHub: https://github.com/pika-0203/Open-Chat-Graph

15万以上のOpenChatを毎時間クロールし、統計データを提供する本格的なWebアプリケーション。MimimalCMSの実践的な使用例として参照できます。

## クイックスタート

### 基本的な使い方

#### ① ページコントローラーを作成

**ファイル:** [`app/Controllers/Pages/TestPageController.php`](app/Controllers/Pages/TestPageController.php)

```php
namespace App\Controllers\Pages;

class TestPageController
{
    public function index()
    {
        return view('test_content', ['message' => 'Hello World!']);
    }
}
```

これだけで `http://example.com/test` からアクセス可能になります。

#### ② ビューテンプレートを作成

**ファイル:** [`app/Views/test_content.php`](app/Views/test_content.php)

```php
<!DOCTYPE html>
<html>
<head>
    <title>Test Page</title>
</head>
<body>
    <h1><?php echo $message ?></h1> <!-- 自動的にエスケープされる -->
</body>
</html>
```

### ルーティング

MimimalCMSは2種類のルーティング方法を提供します。

**コア実装:** [`shadow/Kernel/Route.php`](shadow/Kernel/Route.php), [`shadow/Kernel/Dispatcher/Routing.php`](shadow/Kernel/Dispatcher/Routing.php)

#### 1. 明示的なルート定義

コントローラーとメソッドを明示的に指定する方法。柔軟なルーティングやバリデーションが可能です。

**設定ファイル:** [`app/Config/routing.php`](app/Config/routing.php)

```php
use Shadow\Kernel\Route;

// 基本的なルート定義
Route::path('user/show', [UserController::class, 'show']);

// パスパラメータ付き
Route::path('user/{userId}/profile', [UserController::class, 'profile'])
    ->matchNum('userId', min: 1);

// バリデーションのみ定義（コントローラーは規約ベースで解決）
Route::path('article/{id}')
    ->matchNum('id', min: 1);
// → 規約に従い ArticlePageController::index($id) が呼ばれる

Route::path('article/{id}/edit')
    ->matchNum('id', min: 1)
    ->matchStr('title', maxLen: 200);
// → 規約に従い ArticlePageController::edit($id, $title) が呼ばれる

// 複数のHTTPメソッドに対応
Route::path(
    'article/{id}@get@post',
    [ArticleViewController::class, 'show', 'get'],
    [ArticleUpdateApiController::class, 'update', 'post']
)
    ->matchNum('id', min: 1)
    ->matchStr('title', 'post', maxLen: 200)
    ->middleware([VerifyCsrfToken::class], 'post');

// ルート実行（app/Config/routing.phpの最後に記述）
Route::run();
```

#### 2. 規約ベースのルーティング（デフォルトの挙動）

ルートを明示的に定義しない場合、URLから自動的にコントローラーとメソッドを解決します。

**GETリクエストの場合:**

| URL | コントローラー | メソッド | 配置ファイル |
|-----|--------------|---------|-------------|
| `/` | `App\Controllers\Pages\IndexPageController` | `index` | `app/Controllers/Pages/IndexPageController.php` |
| `/foo` | `App\Controllers\Pages\FooPageController` | `index` | `app/Controllers/Pages/FooPageController.php` |
| `/foo/bar` | `App\Controllers\Pages\FooPageController` | `bar` | `app/Controllers/Pages/FooPageController.php` |

**POST/PUT/DELETE/PATCHリクエストの場合:**

| URL | コントローラー | メソッド | 配置ファイル |
|-----|--------------|---------|-------------|
| `/foo` | `App\Controllers\Api\FooApiController` | `index` | `app/Controllers/Api/FooApiController.php` |
| `/foo/bar` | `App\Controllers\Api\FooApiController` | `bar` | `app/Controllers/Api/FooApiController.php` |

※ GET以外のすべてのHTTPメソッド（POST/PUT/DELETE/PATCH等）は同じApiControllerに解決されます

**規約ルールの制約:**
- パスは**2階層まで**対応（`/path1/path2`）
- パス名は**英数字とアンダースコア**のみ（先頭は英字または`_`）
- 3階層目以降は404エラー
- 存在しないコントローラー/メソッドは404エラー

#### どちらを使うべきか？

| ケース | 推奨方法 |
|--------|---------|
| バリデーションやミドルウェアが必要 | **明示的なルート定義** |
| 3階層以上のURL（例: `/user/123/profile`） | **明示的なルート定義**（必須） |
| シンプルなAPIエンドポイント | 規約ベース |

**明示的なルート定義の2つのパターン:**

1. **コントローラー指定あり**: `Route::path('/foo', [FooController::class, 'index'])`
   - 特定のコントローラーとメソッドを明示

2. **コントローラー指定なし**: `Route::path('/foo')`
   - 規約ベースでコントローラーを解決
   - バリデーションとミドルウェアは適用可能
   - 規約の利便性と安全性を両立

**実践例:** [oc-review-dev/app/Config/routing.php](https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Config/routing.php)

## 主要機能

### 1. ルーティングとバリデーション

**設定ファイル:** [`app/Config/routing.php`](app/Config/routing.php)
**コア実装:** [`shadow/Kernel/Route.php`](shadow/Kernel/Route.php), [`shadow/Kernel/Validator.php`](shadow/Kernel/Validator.php)

#### 基本的なルート定義

```php
use Shadow\Kernel\Route;
use App\Middleware\VerifyCsrfToken;

// 画像アップロードのルート定義
Route::path('image/store@post')
    ->matchFile('file', ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], emptyAble: false)
    ->matchStr('imageType', regex: '/(jpeg|png|webp)/')
    ->matchNum('imageSize', min: 0, max: 1000)
    ->fails(redirect('image'));

// ミドルウェアの実行
Route::run(VerifyCsrfToken::class);
```

#### パスパラメータ付きルーティング

```php
// パスパラメータの定義
Route::path('user/{userId}/profile', [UserController::class, 'show'])
    ->matchNum('userId', min: 1)
    ->matchStr('tab', regex: ['posts', 'followers', 'following'], emptyAble: true);

// カスタムバリデーション
Route::path('search', [SearchController::class, 'index'])
    ->matchStr('q', maxLen: 100)
    ->match(function(string $q) {
        // カスタムロジックでバリデーション
        return strlen(trim($q)) > 0;
    });
```

#### 複数HTTPメソッドへの対応

```php
// GETとPOSTで異なるコントローラーを指定
Route::path(
    'comment/{commentId}@get@post',
    [CommentViewController::class, 'show', 'get'],
    [CommentPostController::class, 'store', 'post']
)
    ->matchNum('commentId', min: 1)
    ->matchStr('text', 'post', maxLen: 1000) // POSTのみバリデーション
    ->matchNum('limit', 'get', min: 1, max: 100, default: 20, emptyAble: true) // GETのみ
    ->middleware([VerifyCsrfToken::class], 'post'); // POSTのみミドルウェア適用
```

**バリデーション機能:**
- `matchStr(string $name, ?string $requestMethod = null, ?int $maxLen = null, string|array|null $regex = null, bool $emptyAble = false, mixed $default = '')`
  - 文字列の検証（正規表現、最大長、空文字許可、デフォルト値）
- `matchNum(string $name, ?string $requestMethod = null, ?int $min = null, ?int $max = null, bool $emptyAble = false, ?int $default = 0)`
  - 数値の検証（最小値、最大値、空値許可、デフォルト値）
- `matchFile(string $name, array $allowedMimeTypes, ?int $maxFileSize = null, bool $emptyAble = false)`
  - ファイルの検証（MIMEタイプ、サイズ制限）
- `match(\Closure $callback, ?string $requestMethod = null)`
  - カスタムバリデーションロジック
- `fails(Response|\Closure $response)`
  - バリデーション失敗時のレスポンス
- `middleware(array $middlewareClasses, ?string $requestMethod = null)`
  - ミドルウェアの適用

**実践例:** [oc-review-dev/app/Config/routing.php](https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/Config/routing.php)

#### フォーム実装例

バリデーションエラー時に入力値を保持し、エラーメッセージを表示する実装例:

**ルーティング設定:**
```php
// app/Config/routing.php
Route::path('contact@get@post', [ContactController::class, 'show', 'get'], [ContactController::class, 'store', 'post'])
    ->matchStr('name', 'post', maxLen: 50)
    ->matchStr('email', 'post', maxLen: 100, regex: '/^[^\s@]+@[^\s@]+\.[^\s@]+$/')
    ->matchStr('message', 'post', maxLen: 1000)
    ->fails(function() {
        // バリデーション失敗時、入力値を保持してリダイレクト
        Session::flash('error', '入力内容に誤りがあります。');
        return redirect('contact');
    })
    ->middleware([VerifyCsrfToken::class], 'post');
```

**コントローラー:**
```php
// app/Controllers/Pages/ContactController.php
class ContactController
{
    public function show()
    {
        return view('contact');
    }

    public function store(Reception $reception)
    {
        // バリデーション済みのデータを取得
        $data = [
            'name' => $reception->input('name'),
            'email' => $reception->input('email'),
            'message' => $reception->input('message'),
        ];

        // メール送信などの処理...

        Session::flash('success', 'お問い合わせを受け付けました。');
        return redirect('contact');
    }
}
```

**ビューテンプレート:**
```php
<!-- app/Views/contact.php -->
<!DOCTYPE html>
<html>
<head>
    <title>お問い合わせ</title>
    <style>
        .error { color: red; margin: 10px 0; }
        .success { color: green; margin: 10px 0; }
        .form-error { border-color: red; }
    </style>
</head>
<body>
    <h1>お問い合わせフォーム</h1>

    <?php if ($error = session('error')): ?>
        <div class="error"><?php echo $error ?></div>
    <?php endif; ?>

    <?php if ($success = session('success')): ?>
        <div class="success"><?php echo $success ?></div>
    <?php endif; ?>

    <form method="POST" action="/contact">
        <?php echo csrf() ?>

        <div>
            <label>お名前（50文字以内）:</label>
            <input type="text" name="name" value="<?php echo old('name') ?>" required>
        </div>

        <div>
            <label>メールアドレス:</label>
            <input type="email" name="email" value="<?php echo old('email') ?>" required>
        </div>

        <div>
            <label>お問い合わせ内容（1000文字以内）:</label>
            <textarea name="message" rows="5" required><?php echo old('message') ?></textarea>
        </div>

        <button type="submit">送信</button>
    </form>
</body>
</html>
```

**動作の流れ:**
1. ユーザーがフォームに入力して送信
2. バリデーションが実行される
3. **バリデーション成功時**: コントローラーの `store()` メソッドが実行される
4. **バリデーション失敗時**:
   - 入力値が自動的にセッションに保存される
   - `fails()` で定義したリダイレクトが実行される
   - フォーム再表示時、`old()` 関数で入力値を復元できる

### 2. ヘルパー関数

**コア実装:** [`shared/MimimalCMS_HelperFunctions.php`](shared/MimimalCMS_HelperFunctions.php)

MimimalCMSは豊富なヘルパー関数を提供し、日常的な開発タスクを簡潔なコードで実現できます。

#### ビューとレスポンス関連

**`view(?string $viewTemplateFile = null, ?array $valuesArray = null): ViewInterface`**

ビューテンプレートをレンダリングします。キー名が`_`で始まる変数以外は自動的にHTMLエスケープされます。

```php
// 基本的な使用
return view('home', ['title' => 'Welcome']);

// ビューの連結（複数のテンプレートを結合）
return view('layouts/header')
    ->make('user/profile', ['user' => $user])
    ->make('layouts/footer');

// テンプレートの存在確認
if (view()->exists('custom/template')) {
    return view('custom/template');
}

// 生のHTMLを出力（エスケープしない）
return view('page', ['_rawHtml' => '<strong>信頼できるHTML</strong>']);
```

**`response(mixed $data, int $responseCode = 200): Response`**

JSONレスポンスを返します。APIエンドポイントの実装に最適です。

```php
// 成功レスポンス
return response(['status' => 'success', 'data' => $data], 200);

// エラーレスポンス
return response(['error' => 'Not found'], 404);

// 作成成功
return response(['id' => $userId, 'message' => 'Created'], 201);
```

**`redirect(?string $url = null, int $responseCode = 302, ?string $urlRoot = null): Response`**

指定したURLへリダイレクトします。

```php
// 相対パスでリダイレクト
return redirect('dashboard');

// ルートにリダイレクト
return redirect();

// 外部URLにリダイレクト
return redirect('https://example.com');

// 異なるステータスコードでリダイレクト
return redirect('login', 301);
```

#### セッションとクッキー管理

**`session(null|string|array $value = null, mixed $default = null): mixed|Session`**

セッションの取得・設定を行います。

```php
// 値を設定
session(['user_id' => 123, 'username' => 'john']);

// 値を取得
$userId = session('user_id');
$username = session('username', 'guest'); // デフォルト値

// ドット記法で階層的にアクセス
session(['user' => ['name' => 'John', 'email' => 'john@example.com']]);
$name = session('user.name'); // 'John'

// Sessionインスタンスを取得（高度な操作用）
$session = session();
$session->forget('user_id'); // セッション削除
$session->flush(); // 全削除
$session->has('user_id'); // 存在確認
```

**`old(?string $key = null): mixed`**

バリデーションエラー時に前回のリクエストの入力値を取得します。フォームの再入力に便利です。

```php
// フォームで以前の入力値を復元
<input type="text" name="email" value="<?php echo old('email') ?>">
<textarea name="message"><?php echo old('message') ?></textarea>

// 全ての古い入力値を取得
$allOldInputs = old();
```

**`cookie(null|string|array $value = null, int $expires = 0, ...): mixed|Cookie`**

クッキーの取得・設定を行います。

```php
// クッキーを設定
cookie(['user_id' => 123, 'theme' => 'dark'], time() + 3600);

// クッキーを取得
$theme = cookie('theme');
$userId = cookie('user_id');

// 詳細なオプション付きで設定
cookie()->push(
    'token',
    'abc123',
    expires: time() + 86400,
    path: '/',
    samesite: 'Strict',
    secure: true,
    httpOnly: true
);

// クッキー削除
cookie()->remove('user_id');
```

#### URL生成

**`url(string|array ...$paths): string`**

現在のサイトのフルURLを生成します。

```php
// 基本的な使用
url('user', 'profile'); // https://example.com/user/profile

// 配列でurlRootを指定
url(['urlRoot' => '/en', 'paths' => ['home', 'about']]);
// https://example.com/en/home/about
```

**`publicDir(string $path = '', ?string $publicDir = null): string`**

publicディレクトリへの絶対パスを返します。

```php
// publicディレクトリのパス
publicDir(); // /var/www/public

// サブディレクトリを含むパス
publicDir('css/styles.css'); // /var/www/public/css/styles.css
publicDir('/images/logo.png'); // /var/www/public/images/logo.png
```

**`fileUrl(string $filePath, ?string $publicDir = null, ?string $urlRoot = null): string`**

キャッシュバスティング用のバージョン付きファイルURLを生成します。

```php
// CSSファイルのURL（キャッシュバスティング付き）
fileUrl('css/styles.css');
// https://example.com/css/styles.css?v=1609459200

// JSファイルのURL
fileUrl('js/app.js');
// https://example.com/js/app.js?v=1609459300
```

**`pagerUrl(string $path, int $pageNumber, ?string $urlRoot = null): string`**

ページネーション用のURLを生成します。

```php
// ページ番号付きURL
pagerUrl('articles', 2); // https://example.com/articles/2
pagerUrl('articles', 1); // https://example.com/articles （1ページ目は省略）
```

**`path(?string $urlRoot = null): string`**

現在のリクエストパスを取得します。

```php
// 現在のパス
$currentPath = path(); // /user/profile
```

#### セキュリティ関連

**`getCsrfToken(): string`**

新しいCSRFトークンを生成し、セッションに保存します。

```php
$token = getCsrfToken();
// セッションに保存され、フォームで使用可能
```

**`csrfField(): void`**

CSRFトークンを含むHTMLのhidden inputを出力します。

```php
<form method="POST">
    <?php csrfField() ?>
    <!-- 出力: <input type="hidden" name="_csrf" value="..." /> -->
    <button type="submit">送信</button>
</form>
```

**`verifyCsrfToken(bool $removeTokenFromSession = false): void`**

リクエストからCSRFトークンを検証します。

```php
// ミドルウェア内で使用
try {
    verifyCsrfToken();
} catch (ValidationException $e) {
    return response(['error' => 'Invalid CSRF token'], 403);
}
```

**`h(mixed $string): string`**

文字列をHTMLエスケープします。

```php
echo h('<script>alert("XSS")</script>');
// 出力: &lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;

echo h(123); // 123（数値はそのまま）
```

**`sanitizeString(string $string): string`**

非ASCII文字を削除します。

```php
$clean = sanitizeString("Hello™ World®");
// "Hello World"
```

**`removeAllZeroWidthCharacters(string $inputString): string`**

ゼロ幅文字を削除し、Unicode正規化を行います。

```php
$normalized = removeAllZeroWidthCharacters($userInput);
```

#### リクエスト情報取得

**`getIP(): string`**

クライアントのIPアドレスを取得します。プロキシやCDN経由でも正しく取得できます。

```php
$clientIp = getIP();
// Cloudflare、プロキシ環境でも正しいIPを取得
```

**`getUA(): string`**

ユーザーエージェントを取得します。

```php
$userAgent = getUA();
// Mozilla/5.0 (Windows NT 10.0; Win64; x64)...
```

#### ファイルとストレージ操作

**`safeFileRewrite(string $targetFile, string $content, int $permissions = 0777): void`**

一時ファイルを使用して安全にファイルを書き換えます。

```php
safeFileRewrite('/path/to/config.json', json_encode($config));
```

**`saveSerializedFile(string $path, mixed $value): void`**

データをシリアライズして圧縮保存します。

```php
$data = ['user' => 'john', 'settings' => [...]];
saveSerializedFile('/path/to/cache.dat', $data);
```

**`getUnserializedFile(string $path): mixed`**

保存されたシリアライズデータを読み込みます。

```php
$data = getUnserializedFile('/path/to/cache.dat');
if ($data === false) {
    // ファイルが存在しないか読み込みエラー
}
```

**`mkdirIfNotExists(string $directory, int $permissions = 0777, bool $recursive = true): void`**

ディレクトリが存在しなければ作成します。

```php
mkdirIfNotExists('/path/to/uploads/images');
```

**`deleteDirectory(string $dir): bool`**

ディレクトリとその中身を再帰的に削除します。

```php
if (deleteDirectory('/path/to/temp')) {
    echo "削除成功";
}
```

**`deleteStorageFile(string $filename, bool $fullPath = false): bool`**

storageディレクトリからファイルを削除します。

```php
deleteStorageFile('cache/user_123.dat');
```

**`deleteStorageFileAll(string $path, bool $fullPath = false): void`**

指定したパスのファイルを全て削除します。

```php
deleteStorageFileAll('cache/*');
```

**`getStorageFileList(string $path, string $pattern = '/*.*', bool $fullPath = false): array`**

storageディレクトリのファイル一覧を取得します。

```php
$files = getStorageFileList('uploads/images', '/*.jpg');
// ['uploads/images/photo1.jpg', 'uploads/images/photo2.jpg']
```

**`getFilesWithExtension(string $dir, string $ext): CallbackFilterIterator`**

特定の拡張子を持つファイルをディレクトリから再帰的に取得します。

```php
$txtFiles = getFilesWithExtension('/path/to/docs', 'txt');
foreach ($txtFiles as $file) {
    echo $file->getRealPath();
}
```

#### ユーティリティ

**`app(?string $abstract = null, array $parameters = []): object|Application`**

DIコンテナからインスタンスを取得します。

```php
// サービスクラスのインスタンスを取得
$service = app(UserService::class);

// Applicationインスタンス自体を取得
$app = app();
$app->bind(Interface::class, Implementation::class);
```

**`getClassSimpleName(string|object $fullyQualifiedClassName): string`**

完全修飾クラス名からクラス名のみを取得します。

```php
$name = getClassSimpleName('App\Controllers\UserController');
// "UserController"
```

**`base62Hash(string $str, string $alg = 'fnv1a64'): string`**

文字列からbase62ハッシュを生成します。短いユニークIDの生成に便利です。

```php
$hash = base62Hash('unique-identifier');
// "aB3xK9p..."
```

**`isWithinHalfExpires(int $futureUnixTime, int $expirationTimeInSeconds): bool`**

有効期限の半分以内かどうかをチェックします。キャッシュのリフレッシュタイミングに便利です。

```php
if (isWithinHalfExpires($cacheExpires, 3600)) {
    // キャッシュの更新時期
    refreshCache();
}
```

**`getScriptExecutionTime(?float $start = null): float`**

スクリプトの実行時間を測定します。

```php
$start = getScriptExecutionTime();
// 処理を実行
$elapsed = getScriptExecutionTime($start);
echo "実行時間: {$elapsed}ms";
```

**`debug(...$vars): void`**

デバッグ用に変数をコンソールに出力します。

```php
debug($user, $settings, $config);
```

**`pre_var_dump($var): void`**

変数を整形して出力します。

```php
pre_var_dump($array);
// <pre>
// array(...)
// </pre>
```

### 3. 依存性注入コンテナ

**コア実装:** [`shadow/Kernel/Application.php`](shadow/Kernel/Application.php), [`shadow/Kernel/Dispatcher/ConstructorInjection.php`](shadow/Kernel/Dispatcher/ConstructorInjection.php)

#### インターフェースバインディング

**設定ファイル:** [`shared/MimimalCmsConfig.php`](shared/MimimalCmsConfig.php)

```php
class MimimalCmsConfig
{
    public static array $constructorInjectionMap = [
        UserRepositoryInterface::class => UserRepository::class,
        CacheInterface::class => RedisCache::class,
    ];
}
```

#### 自動コンストラクタインジェクション

```php
// リポジトリインターフェース
interface UserRepositoryInterface
{
    public function findById(int $id): ?User;
    public function save(User $user): bool;
}

// 実装クラス
class UserRepository implements UserRepositoryInterface
{
    public function __construct(private DBInterface $db) {}

    public function findById(int $id): ?User
    {
        $stmt = $this->db->execute("SELECT * FROM users WHERE id = :id", [':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}

// サービスクラス（自動インジェクション）
class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private CacheInterface $cache
    ) {}

    public function getUser(int $id): ?User
    {
        return $this->cache->remember("user:{$id}", function() use ($id) {
            return $this->userRepository->findById($id);
        });
    }
}

// コントローラー（自動インジェクション）
class UserController
{
    public function __construct(private UserService $userService) {}

    public function show(int $id)
    {
        $user = $this->userService->getUser($id);
        return view('user.profile', ['user' => $user]);
    }
}
```

#### 手動でのDI使用

```php
use Shadow\Kernel\Application;

$app = new Application();

// インターフェースと実装をバインド
$app->bind(PaymentGatewayInterface::class, StripePaymentGateway::class);

// シングルトン登録
$app->singleton(CacheManager::class);

// クロージャでバインド
$app->bind(Logger::class, function($app) {
    return new FileLogger('/path/to/log');
});

// インスタンス生成（依存関係を自動解決）
$service = $app->make(PaymentService::class);
```

**実践例:** [oc-review-dev/shared/MimimalCmsConfig.php](https://github.com/pika-0203/Open-Chat-Graph/blob/main/shared/MimimalCmsConfig.php)

### 3.5 ServiceProviderパターン

**コア実装:** [`shadow/Kernel/Application.php`](shadow/Kernel/Application.php)

ServiceProviderを使用して、アプリケーションの起動時や特定のルートでのみ動的にサービスをバインドできます。

#### ServiceProviderインターフェース

```php
interface ServiceProviderInterface
{
    public function register(): void;
}
```

#### 基本的なServiceProviderの実装

```php
// app/ServiceProvider/CacheServiceProvider.php
namespace App\ServiceProvider;

use App\Services\Cache\CacheInterface;
use App\Services\Cache\RedisCache;
use App\Services\Cache\FileCache;

class CacheServiceProvider implements ServiceProviderInterface
{
    public function register(): void
    {
        // 環境に応じて異なる実装をバインド
        if (getenv('CACHE_DRIVER') === 'redis') {
            app()->singleton(CacheInterface::class, fn() => new RedisCache(
                host: getenv('REDIS_HOST'),
                port: getenv('REDIS_PORT')
            ));
        } else {
            app()->singleton(CacheInterface::class, fn() => new FileCache(
                cacheDir: __DIR__ . '/../../storage/cache'
            ));
        }
    }
}
```

#### ルーティングでの動的ServiceProvider登録

特定のエンドポイントでのみ異なる実装に切り替える:

```php
// app/Config/routing.php

// API専用のデータベース実装に切り替え
Route::path('api/v1/{resource}', [ApiController::class, 'index'])
    ->match(function () {
        // API用のServiceProviderを登録
        app(ApiServiceProvider::class)->register();
        return true;
    });

// 管理者専用の実装に切り替え
Route::path('admin/dashboard', [AdminController::class, 'index'])
    ->match(function (AdminAuthService $auth) {
        if ($auth->authenticate()) {
            app(AdminServiceProvider::class)->register();
            return true;
        }
        return false;
    });
```

#### 実践例: テスト環境とプロダクション環境の切り替え

```php
// app/ServiceProvider/RepositoryServiceProvider.php
class RepositoryServiceProvider implements ServiceProviderInterface
{
    public function register(): void
    {
        if (MimimalCmsConfig::$debugMode) {
            // テスト環境: モックリポジトリを使用
            app()->bind(UserRepositoryInterface::class, MockUserRepository::class);
            app()->bind(PaymentGatewayInterface::class, MockPaymentGateway::class);
        } else {
            // プロダクション環境: 実際の実装を使用
            app()->bind(UserRepositoryInterface::class, UserRepository::class);
            app()->bind(PaymentGatewayInterface::class, StripePaymentGateway::class);
        }
    }
}

// shared/MimimalCMS_Settings.php で起動時に登録
app(RepositoryServiceProvider::class)->register();
```

#### 複数データベースの切り替え

```php
// app/ServiceProvider/DatabaseServiceProvider.php
class DatabaseServiceProvider implements ServiceProviderInterface
{
    public function __construct(private string $database) {}

    public function register(): void
    {
        match ($this->database) {
            'mysql' => app()->bind(DBInterface::class, fn() => new MySQLDatabase()),
            'sqlite' => app()->bind(DBInterface::class, fn() => new SQLiteDatabase()),
            'postgres' => app()->bind(DBInterface::class, fn() => new PostgresDatabase()),
        };
    }
}

// ルーティングで動的に切り替え
Route::path('analytics/{report}', [AnalyticsController::class, 'show'])
    ->match(function () {
        // 分析用のSQLiteデータベースに切り替え
        app(new DatabaseServiceProvider('sqlite'))->register();
        return true;
    });
```

**実践例:** [Open-Chat-Graph/app/ServiceProvider/ApiDbOpenChatControllerServiceProvider.php](https://github.com/pika-0203/Open-Chat-Graph/blob/main/app/ServiceProvider/ApiDbOpenChatControllerServiceProvider.php)

### 4. データベース (PDOラッパー)

**コア実装:** [`shadow/DB.php`](shadow/DB.php)

型安全なパラメータバインディングを持つPDOラッパー:

```php
use Shadow\DB;

// クエリ実行
$stmt = DB::execute(
    "SELECT * FROM users WHERE age > :age AND status = :status",
    [':age' => 20, ':status' => 'active']
);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// INSERTとIDの取得
$lastId = DB::executeAndGetLastInsertId(
    "INSERT INTO users (name, email) VALUES (:name, :email)",
    [':name' => 'John', ':email' => 'john@example.com']
);

// トランザクション
DB::transaction(function() {
    DB::execute("UPDATE accounts SET balance = balance - 100 WHERE id = 1");
    DB::execute("UPDATE accounts SET balance = balance + 100 WHERE id = 2");
});

// 生のPDOインスタンス取得
$pdo = DB::connect();
```

**データベース設定:** [`shared/MimimalCmsConfig.php`](shared/MimimalCmsConfig.php)

```php
class MimimalCmsConfig
{
    public static string $dbHost = 'localhost';
    public static string $dbName = 'your_database';
    public static string $dbUserName = 'your_username';
    public static string $dbPassword = 'your_password';
    public static bool $dbAttrPersistent = false;
    public static string $dbCharset = 'utf8mb4';
}
```

### 5. ビューとテンプレート

**コア実装:** [`shadow/Kernel/View.php`](shadow/Kernel/View.php)
**設定:** `MimimalCmsConfig::$viewsDir` (デフォルト: `app/Views`)

#### テンプレートの配置規約

- テンプレートは `app/Views` ディレクトリに配置
- `.php` または `.html` 拡張子を使用
- サブディレクトリ構造をサポート
- 拡張子は省略可能（`.php` > `.html` の優先順位で自動検索）

```
app/Views/
├── layouts/
│   ├── header.php
│   └── footer.php
├── components/          # 再利用可能なコンポーネント
│   ├── navbar.php
│   └── breadcrumb.php
├── user/
│   ├── profile.php
│   └── settings.php
├── errors/              # エラーページ（専用ページ優先）
│   ├── 404.php         # 404専用
│   ├── 500.php         # 500専用
│   └── error.php       # 汎用エラーページ（フォールバック）
├── home.php
└── about.php
```

#### ビューの基本的な使い方

```php
// コントローラー
return view('home', ['title' => 'Welcome']);

// サブディレクトリのテンプレート
return view('user/profile', ['user' => $user]);

// 拡張子の省略
view('home');        // → app/Views/home.php または home.html
view('user/profile'); // → app/Views/user/profile.php

// ビューの存在確認
if (view()->exists('custom_theme/header')) {
    return view('custom_theme/header');
} else {
    return view('default/header');
}

// ビューの連結
return view('layouts/header')
    ->make('user/profile', ['user' => $user])
    ->make('layouts/footer');
```

#### 自動XSSエスケープ

**全ての変数は自動的にエスケープされます:**

```php
// コントローラー
return view('profile', [
    'name' => '<script>alert("XSS")</script>',
    '_rawHtml' => '<strong>信頼できるHTML</strong>', // アンダースコアで始まるキーはエスケープされない
    'count' => 123,                                   // 数値はそのまま
    'active' => true,                                 // bool値もそのまま
    'nested' => [
        'html' => '<div>test</div>',                  // ネストした配列もエスケープ
        '_safeHtml' => '<span>safe</span>'           // ネスト内でも_プレフィックスは有効
    ]
]);
```

```php
<!-- app/Views/profile.php -->
<h1><?php echo $name ?></h1>
<!-- 出力: <h1>&lt;script&gt;alert("XSS")&lt;/script&gt;</h1> -->

<div><?php echo $_rawHtml ?></div>
<!-- 出力: <div><strong>信頼できるHTML</strong></div> -->

<p>カウント: <?php echo $count ?></p>
<!-- 出力: <p>カウント: 123</p> -->

<p><?php echo $nested['html'] ?></p>
<!-- 出力: <p>&lt;div&gt;test&lt;/div&gt;</p> -->
```

**エスケープされないケース:**
- **アンダースコアプレフィックス**: キー名が `_` で始まる変数
- **数値・bool・null**: 文字列以外の型
- **Enum値**: `\UnitEnum` インスタンス
- **Viewインスタンス**: `ViewInterface` 実装クラス

### 6. セキュリティ機能

#### CSRFトークン保護

**コア実装:** [`shadow/Kernel/Cookie.php`](shadow/Kernel/Cookie.php), [`shadow/Kernel/Session.php`](shadow/Kernel/Session.php)
**実装例:** [`app/Middleware/VerifyCsrfToken.php`](app/Middleware/VerifyCsrfToken.php)

```php
// ミドルウェアで自動検証
use Shadow\Kernel\Reception;

class VerifyCsrfToken
{
    public function handle(Reception $reception)
    {
        if ($reception->isMethod('POST')) {
            $token = $reception->input('_token');

            if (!hash_equals(session('_token'), $token)) {
                throw new UnauthorizedException('Invalid CSRF token');
            }
        }
    }
}
```

```php
<!-- フォームにトークンを埋め込む -->
<form method="POST" action="/submit">
    <?php echo csrf() ?>
    <input type="text" name="data">
    <button type="submit">送信</button>
</form>
```

#### 文字列暗号化

**コア実装:** [`shadow/StringCryptor.php`](shadow/StringCryptor.php)

HKDF + AES-256-GCMによる安全な暗号化:

```php
use Shadow\StringCryptor;

$cryptor = new StringCryptor();

// 暗号化
$encrypted = $cryptor->encrypt('sensitive data');

// 復号化
$decrypted = $cryptor->decrypt($encrypted);
```

**設定:** [`shared/MimimalCmsConfig.php`](shared/MimimalCmsConfig.php)

```php
class MimimalCmsConfig
{
    public static string $stringCryptorHkdfKey = 'YOUR_SECRET_KEY';
    public static string $stringCryptorOpensslKey = 'YOUR_OPENSSL_KEY';
}
```

### 7. 画像処理

**コア実装:** [`shadow/File/FileValidator.php`](shadow/File/FileValidator.php)

安全な画像検証とリサイズ機能:

```php
use Shadow\File\FileValidator;

$validator = new FileValidator();

// 画像の検証とリサイズ
$result = $validator->validateImageFileAndResize(
    $_FILES['image']['tmp_name'],
    $_FILES['image']['name'],
    maxWidth: 1200,
    maxHeight: 800,
    quality: 85
);

// 保存
move_uploaded_file($result['tmp_name'], "uploads/{$result['hashed_name']}");
```

**設定:**

```php
class MimimalCmsConfig
{
    // デフォルトの最大ファイルサイズ（KB）
    public static int $defaultMaxFileSize = 20480; // 20MB
}
```

### 8. セッション管理

**コア実装:** [`shadow/Kernel/Session.php`](shadow/Kernel/Session.php)

```php
// セッションに保存
session(['user_id' => 123, 'username' => 'john']);

// 取得
$userId = session('user_id');
$username = session('username', 'guest'); // デフォルト値

// ドット記法で階層的にアクセス
session(['user' => ['name' => 'John', 'email' => 'john@example.com']]);
$name = session('user.name'); // 'John'

// フラッシュメッセージ（次のリクエストまで保持）
Session::flash('success', '保存しました');
$message = session('success');

// セッション削除
session()->forget('user_id');
session()->flush(); // 全削除

// セッションの存在確認
if (session()->has('user_id')) {
    // ...
}
```

### 9. クッキー管理

**コア実装:** [`shadow/Kernel/Cookie.php`](shadow/Kernel/Cookie.php)

```php
use Shadow\Kernel\Cookie;

// クッキーに保存
Cookie::push('user_id', 123);
Cookie::push('username', 'john', time() + 3600); // 1時間有効

// 複数のクッキーを一度に設定
Cookie::push(['user_id' => 123, 'username' => 'john'], time() + 3600);

// 取得
$userId = Cookie::get('user_id');
$username = Cookie::get('username', 'guest'); // デフォルト値

// クッキー削除
Cookie::remove('user_id');
Cookie::flush(); // 全削除

// クッキーの存在確認
if (Cookie::has('user_id')) {
    // ...
}

// オプション付きでクッキーを設定
Cookie::push(
    'token',
    'abc123',
    expires: time() + 86400,      // 有効期限
    path: '/',                     // パス
    samesite: 'Strict',            // SameSite属性
    secure: true,                  // Secure属性
    httpOnly: true,                // HttpOnly属性
    domain: 'example.com'          // ドメイン
);
```

### 10. リクエスト処理

**コア実装:** [`shadow/Kernel/Reception.php`](shadow/Kernel/Reception.php)

```php
use Shadow\Kernel\Reception;

class MyController
{
    public function handle(Reception $reception)
    {
        // リクエストメソッドの確認
        if ($reception->isMethod('POST')) {
            // ...
        }

        // 入力データ取得
        $email = $reception->input('email');
        $user = $reception->input('user.name'); // ドット記法

        // すべての入力データ
        $allData = $reception->input();

        // 入力の存在確認
        if ($reception->has('email')) {
            // ...
        }

        // オブジェクトとして取得
        $userObj = $reception->getObject('user');
        // $userObj->name, $userObj->email でアクセス可能

        // JSONリクエストの判定
        if ($reception->isJson()) {
            // ...
        }
    }
}
```

### 11. ミドルウェア

**コア実装:** [`shadow/Kernel/Dispatcher/MiddlewareInvoker.php`](shadow/Kernel/Dispatcher/MiddlewareInvoker.php)

リクエスト処理の前後で実行されるミドルウェア:

```php
namespace App\Middleware;

use Shadow\Kernel\Reception;

class AuthMiddleware
{
    public function handle(Reception $reception)
    {
        $userId = session('user_id');

        if (!$userId) {
            throw new UnauthorizedException('認証が必要です');
        }

        // ユーザー情報を取得してリクエストに追加
        $reception->overWrite(array_merge(
            $reception->input(),
            ['authenticated_user_id' => $userId]
        ));
    }
}
```

#### ミドルウェアの戻り値による挙動

ミドルウェアの `handle()` メソッドは、戻り値によって異なる動作を行います:

| 戻り値 | 挙動 |
|--------|------|
| `null` または `void` | 処理を続行（次のミドルウェアやコントローラーへ） |
| `false` | エラーレスポンス（`fails()` で設定したレスポンスを返す） |
| `array` | `Reception::$inputData` にマージして処理を続行 |
| `ViewInterface` | ビューを表示して処理を終了 |
| `ResponseInterface` | レスポンスを送信して処理を終了 |
| `\Closure` | クロージャを実行して処理を終了 |

```php
// パターン1: 処理を続行
class LoggingMiddleware
{
    public function handle(Reception $reception)
    {
        error_log('Request: ' . $reception->method());
        return; // または return null;
    }
}

// パターン2: データをマージして続行
class UserDataMiddleware
{
    public function handle(Reception $reception)
    {
        $userId = session('user_id');

        // ユーザー情報をリクエストデータに追加
        return ['user_id' => $userId, 'is_admin' => $this->checkAdmin($userId)];
    }
}

// パターン3: 条件付きエラーレスポンス
class RateLimitMiddleware
{
    public function handle(Reception $reception)
    {
        if ($this->isRateLimited()) {
            return false; // fails()のレスポンスを返す
        }
        return null; // 処理を続行
    }
}

// パターン4: リダイレクト
class GuestMiddleware
{
    public function handle(Reception $reception)
    {
        if (session('user_id')) {
            return redirect('dashboard'); // ResponseInterface
        }
    }
}

// パターン5: エラービューの表示
class MaintenanceMiddleware
{
    public function handle(Reception $reception)
    {
        if ($this->isMaintenanceMode()) {
            return view('errors/maintenance'); // ViewInterface
        }
    }
}
```

## リクエストライフサイクル

**コア実装:** [`shadow/Kernel/Kernel.php`](shadow/Kernel/Kernel.php)

```
1. リクエスト受信
   ↓
2. URIパース (RequestParser)
   ↓
3. ルーティング (Routing::resolveController)
   ↓
4. パラメータバリデーション (Validator)
   ↓
5. ミドルウェア実行 (MiddlewareInvoker)
   ↓
6. コントローラー実行 (ControllerInvoker + ConstructorInjection)
   ↓
7. レスポンス処理 (ResponseHandler)
   ↓
8. レスポンス送信
```

## ディレクトリ構造

```
/
├── app/                          # アプリケーションコード
│   ├── Config/                  # 設定ファイル
│   │   ├── routing.php         # ルーティング定義
│   │   └── AppConfig.php       # アプリケーション設定
│   ├── Controllers/            # コントローラー
│   │   ├── Pages/             # ページコントローラー (GET)
│   │   │   └── IndexPageController.php
│   │   └── Api/               # APIコントローラー (POST/PUT/DELETE)
│   │       └── IndexApiController.php
│   ├── Models/                # モデル・リポジトリ
│   ├── Services/              # ビジネスロジック
│   ├── Views/                 # ビューテンプレート (.php/.html)
│   │   ├── layouts/
│   │   └── home.php
│   ├── Middleware/            # ミドルウェア
│   │   └── VerifyCsrfToken.php
│   ├── Exceptions/            # 例外ハンドラー
│   │   └── Handlers/
│   │       └── ApplicationExceptionHandler.php
│   └── Helpers/               # ヘルパー関数
│       └── functions.php
│
├── shadow/                      # フレームワークコア
│   ├── Kernel/                # カーネル・ルーティング・DIコンテナ
│   │   ├── Application.php    # DIコンテナ
│   │   ├── Kernel.php         # リクエストハンドラー
│   │   ├── Route.php          # ルーティング定義
│   │   ├── View.php           # ビューエンジン
│   │   ├── Session.php        # セッション管理
│   │   ├── Cookie.php         # クッキー管理
│   │   ├── Reception.php      # リクエスト処理
│   │   ├── Validator.php      # バリデーター
│   │   └── Dispatcher/        # ディスパッチャー
│   │       ├── ConstructorInjection.php  # DI実装
│   │       ├── Routing.php               # ルーティング解決
│   │       └── ControllerInvoker.php     # コントローラー起動
│   ├── File/                  # ファイル・画像処理
│   │   └── FileValidator.php
│   ├── DB.php                 # データベースラッパー
│   └── StringCryptor.php      # 暗号化機能
│
├── shared/                      # 共有設定
│   ├── MimimalCMS_HelperFunctions.php  # グローバルヘルパー関数
│   ├── MimimalCmsConfig.php            # フレームワーク設定
│   ├── MimimalCMS_ExceptionHandler.php # 例外ハンドラー
│   └── Exceptions/                      # 共通例外クラス
│       ├── NotFoundException.php
│       ├── ValidationException.php
│       └── UnauthorizedException.php
│
├── public/                      # 公開ディレクトリ
│   ├── index.php               # エントリーポイント
│   └── assets/                 # 静的ファイル (CSS/JS/画像)
│
├── tests/                       # テストコード
├── .htaccess                    # Apache設定
├── composer.json                # Composer設定
└── README.md
```

## インストール

### 要件

- PHP 8.1以上
- Composer
- MySQL/MariaDB (オプション)
- Apache または Nginx

### セットアップ

```bash
# リポジトリのクローン
git clone https://github.com/mimimiku778/MimimalCMS.git
cd MimimalCMS/www/html

# 依存関係のインストール
composer install

# 設定ファイルの編集
# shared/MimimalCmsConfig.php でデータベース接続などを設定
```

### Webサーバー設定

#### Apache (.htaccess)

プロジェクトには既に[`.htaccess`](.htaccess)が含まれています。

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
```

#### Nginx

```nginx
server {
    listen 80;
    server_name example.com;
    root /path/to/MimimalCMS/www/html/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## 実践例: ユーザー管理機能

### 1. リポジトリの作成

```php
// app/Models/UserRepositoryInterface.php
interface UserRepositoryInterface
{
    public function findById(int $id): ?array;
    public function findByEmail(string $email): ?array;
    public function create(array $data): int;
    public function update(int $id, array $data): bool;
}

// app/Models/UserRepository.php
use Shadow\DB;

class UserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?array
    {
        $stmt = DB::execute(
            "SELECT * FROM users WHERE id = :id",
            [':id' => $id]
        );
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = DB::execute(
            "SELECT * FROM users WHERE email = :email",
            [':email' => $email]
        );
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create(array $data): int
    {
        return DB::executeAndGetLastInsertId(
            "INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :password)",
            [
                ':name' => $data['name'],
                ':email' => $data['email'],
                ':password' => password_hash($data['password'], PASSWORD_BCRYPT)
            ]
        );
    }

    public function update(int $id, array $data): bool
    {
        $stmt = DB::execute(
            "UPDATE users SET name = :name, email = :email WHERE id = :id",
            [':id' => $id, ':name' => $data['name'], ':email' => $data['email']]
        );
        return $stmt->rowCount() > 0;
    }
}
```

### 2. サービスの作成

```php
// app/Services/UserService.php
class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function registerUser(string $name, string $email, string $password): int
    {
        // バリデーション
        if (empty($name) || empty($email) || empty($password)) {
            throw new InvalidInputException('すべてのフィールドは必須です');
        }

        // メールアドレスの重複チェック
        if ($this->userRepository->findByEmail($email)) {
            throw new InvalidInputException('このメールアドレスは既に登録されています');
        }

        // ユーザー作成
        return $this->userRepository->create([
            'name' => $name,
            'email' => $email,
            'password' => $password
        ]);
    }

    public function getUser(int $id): array
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            throw new NotFoundException('ユーザーが見つかりません');
        }

        return $user;
    }
}
```

### 3. ページコントローラーの作成

```php
// app/Controllers/Pages/UserPageController.php
namespace App\Controllers\Pages;

class UserPageController
{
    public function __construct(
        private UserService $userService
    ) {}

    public function index()
    {
        // ユーザー登録フォームを表示
        return view('user/register');
    }

    public function profile(int $userId)
    {
        try {
            $user = $this->userService->getUser($userId);
            return view('user/profile', ['user' => $user]);
        } catch (NotFoundException $e) {
            return view('errors/404');
        }
    }
}
```

### 4. APIコントローラーの作成

```php
// app/Controllers/Api/UserApiController.php
namespace App\Controllers\Api;

class UserApiController
{
    public function __construct(
        private UserService $userService
    ) {}

    public function index()
    {
        $name = input('name');
        $email = input('email');
        $password = input('password');

        try {
            $userId = $this->userService->registerUser($name, $email, $password);
            return response([
                'id' => $userId,
                'message' => 'ユーザーを作成しました'
            ], 201);
        } catch (InvalidInputException $e) {
            return response(['error' => $e->getMessage()], 400);
        }
    }
}
```

### 5. ルーティング設定

```php
// app/Config/routing.php
use Shadow\Kernel\Route;
use App\Middleware\VerifyCsrfToken;

// ユーザー登録（GETとPOST両対応）
Route::path(
    'user@get@post',
    [UserPageController::class, 'index', 'get'],
    [UserApiController::class, 'index', 'post']
)
    ->matchStr('name', 'post', maxLen: 100)
    ->matchStr('email', 'post', maxLen: 255, regex: '/^[^\s@]+@[^\s@]+\.[^\s@]+$/')
    ->matchStr('password', 'post', maxLen: 255, regex: '/^.{8,}$/') // 最低8文字
    ->middleware([VerifyCsrfToken::class], 'post')
    ->fails(response(['error' => 'Invalid input'], 400));

// ユーザープロフィール
Route::path('user/{userId}/profile', [UserPageController::class, 'profile'])
    ->matchNum('userId', min: 1);

Route::run();
```

### 6. ビューテンプレート

```php
<!-- app/Views/user/register.php -->
<!DOCTYPE html>
<html>
<head>
    <title>ユーザー登録</title>
</head>
<body>
    <h1>ユーザー登録</h1>

    <?php if ($success = session('success')): ?>
        <div class="success"><?php echo $success ?></div>
    <?php endif; ?>

    <?php if ($error = session('error')): ?>
        <div class="error"><?php echo $error ?></div>
    <?php endif; ?>

    <form method="POST" action="/user">
        <?php echo csrf() ?>

        <div>
            <label>名前:</label>
            <input type="text" name="name" required>
        </div>

        <div>
            <label>メールアドレス:</label>
            <input type="email" name="email" required>
        </div>

        <div>
            <label>パスワード:</label>
            <input type="password" name="password" required minlength="8">
        </div>

        <button type="submit">登録</button>
    </form>
</body>
</html>
```

```php
<!-- app/Views/user/profile.php -->
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $user['name'] ?>のプロフィール</title>
</head>
<body>
    <h1><?php echo $user['name'] ?></h1>
    <p>メールアドレス: <?php echo $user['email'] ?></p>
    <p>登録日: <?php echo $user['created_at'] ?></p>
</body>
</html>
```

### 7. DIコンテナの設定

```php
// shared/MimimalCmsConfig.php
class MimimalCmsConfig
{
    public static array $constructorInjectionMap = [
        // リポジトリのバインディング
        UserRepositoryInterface::class => UserRepository::class,

        // フレームワーク標準のバインディング
        \Shadow\Kernel\ViewInterface::class => \Shadow\Kernel\View::class,
        \Shadow\DBInterface::class => \Shadow\DB::class,
    ];

    // その他の設定...
}
```

## 例外処理

**コア実装:** [`shared/MimimalCMS_ExceptionHandler.php`](shared/MimimalCMS_ExceptionHandler.php)

カスタム例外ハンドラーでエラーを統一的に処理:

```php
// app/Exceptions/Handlers/ApplicationExceptionHandler.php
namespace App\Exceptions\Handlers;

class ApplicationExceptionHandler implements ApplicationExceptionHandlerInterface
{
    public function handle(\Throwable $e)
    {
        // ログ記録
        error_log($e->getMessage());

        // 開発環境では詳細表示
        if (MimimalCmsConfig::$debugMode) {
            throw $e;
        }

        // 本番環境では汎用エラーページ
        return view('errors/error', [
            'message' => 'エラーが発生しました',
            'code' => $e->getCode()
        ]);
    }
}
```

**HTTPエラーマッピング:** [`shared/MimimalCmsConfig.php`](shared/MimimalCmsConfig.php)

```php
class MimimalCmsConfig
{
    public static array $httpErrors = [
        NotFoundException::class =>         ['httpCode' => 404, 'log' => false, 'httpStatusMessage' => 'Not Found'],
        ValidationException::class =>       ['httpCode' => 400, 'log' => true,  'httpStatusMessage' => 'Bad Request'],
        UnauthorizedException::class =>     ['httpCode' => 401, 'log' => true,  'httpStatusMessage' => 'Unauthorized'],
        ThrottleRequestsException::class => ['httpCode' => 429, 'log' => true,  'httpStatusMessage' => 'Too Many Requests'],
    ];

    // エラーログの設定
    public static string $exceptionLogDirectory = __DIR__ . '/../storage/exception.log';
    public static bool $exceptionHandlerDisplayErrorTraceDetails = true;  // 本番: false
}
```

#### エラーページのカスケード検索

エラーが発生した際、以下の優先順位でエラーページを検索して表示します:

```
1. app/Views/errors/{httpCode}.php  (例: 404.php, 500.php)
   ↓ 見つからない場合
2. app/Views/errors/error.php       (汎用エラーページ)
   ↓ 見つからない場合
3. デフォルトのエラーメッセージ
```

**エラーページの実装例:**

```php
<!-- app/Views/errors/404.php -->
<!DOCTYPE html>
<html>
<head>
    <title>404 Not Found</title>
</head>
<body>
    <h1>ページが見つかりません</h1>
    <p>お探しのページは存在しないか、移動した可能性があります。</p>
    <a href="/">トップページへ戻る</a>
</body>
</html>
```

```php
<!-- app/Views/errors/error.php （汎用） -->
<!DOCTYPE html>
<html>
<head>
    <title>エラー</title>
</head>
<body>
    <h1>エラーが発生しました</h1>
    <p><?php echo $httpStatusMessage ?? 'Internal Server Error' ?></p>
    <?php if (isset($detailsMessage)): ?>
        <p><?php echo $detailsMessage ?></p>
    <?php endif; ?>
</body>
</html>
```

## ドキュメント

詳細なAPIドキュメント:
- [Application (DIコンテナ)](https://mimimiku778.github.io/MimimalCMS-document/packages/Application.html)
- [ヘルパー関数](https://mimimiku778.github.io/MimimalCMS-document/files/shared-mimimalcms-helperfunctions.html)

## ライセンス

MIT License - 詳細は [LICENSE.md](LICENSE.md) を参照してください。

## 作者

mimimiku778 <0203.sub@gmail.com>

## 参考リンク

- [オプチャグラフ (実装例)](https://github.com/pika-0203/Open-Chat-Graph)
- [GitHub Repository](https://github.com/mimimiku778/MimimalCMS)
- [GitHub Issues](https://github.com/mimimiku778/MimimalCMS/issues)
