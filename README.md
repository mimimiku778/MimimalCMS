# MimimalCMS
This is a super simple and minimal CMS.

The first official release version of MimimalCMS is now available, and this will be the final version of the most primitive version.

## Features include:
- A micro-framework based on the MVC model
- Dynamic and static routing of controllers
- Easy definition of validation classes for string, numeric, and file format/size parameters during routing
- Middleware loading
- Basic functionality for building RESTful APIs with just a few lines of code
- Input/output and helper function calls for cookies, sessions, and flash sessions
- A wrapper class for PDO
- A string encryption class using HKDF hash and AES
- A class for safest image validation, automatic resizing, and saving
- Basic exception handler
- Built-in middleware for automatically managing CSRF tokens
- A simple View class consisting of output buffer control and sanitization features

[https://mimimiku778.github.io/MimimalCMS-document/packages/Application.html](https://mimimiku778.github.io/MimimalCMS-document/packages/Application.html)  

[https://mimimiku778.github.io/MimimalCMS-document/files/shared-mimimalcms-helperfunctions.html](https://mimimiku778.github.io/MimimalCMS-document/files/shared-mimimalcms-helperfunctions.html)

<br>
<br>

## How to Use<br>
① Create a file named `PageName`PageController.php in the app/Controllers/Pages directory.<br>
<br>
That's it! You can now display it from http://example.com/ `pagename`. No special configuration is required other than implementing the page.<br>
<br>

### Points<br>
① Implement Class `PageName`PageController in app/Controllers/Pages/`PageName`PageController.php.<br>
② Implement the index method.<br>
```
app/Controllers/Pages/IndexPageController.php

namespace App\Controllers\Pages;

class IndexPageController
{
    public function index()
    {
        return view('home');
    }
}
```
<br>
<br>

## 使い方<br>
① app/Controllers/Pages ディレクトリに `ページ名`PageController.php ファイルを作成する。<br>
<br>
これだけで http://example.com/ `ページ名` から表示できます。<br>
ページの実装以外に特別な設定は不要です。<br>
<br>

### ポイント<br>
① Controllers/Pages/`ページ名`PageController.php に、 Class `ページ名`PageController を実装する。<br>
② indexメソッドを実装する。<br>
```
app/Controllers/Pages/IndexPageController.php

namespace App\Controllers\Pages;

class IndexPageController
{
    public function index()
    {
        return view('home');
    }
}
```
<br>
<br>

### When accessing http://example.com/<br>
The default app/Controllers/Pages/IndexPageController.php will open.<br>
IndexPageController will be instantiated and the index method will be executed.<br>
This is the controller for the default top page.<br>
<br>
<br>

### When accessing http://example.com/foo<br>
If app/Controllers/Pages/FooPageController.php exists, it will be loaded.<br>
FooPageController will be instantiated and the index method will be executed.<br>
If the file does not exist, a 404 error will be returned.<br>
<br>
<br>

### When accessing http://example.com/foo/bar<br>
If FooPageController.php is opened and the bar method is implemented, it will be executed.<br>
If there is a second path, a method with the same name as the second path will be executed instead of index.<br>
If the method does not exist, a 404 error will be returned.<br>
Of course, if FooPageController.php does not exist, a 404 error will be returned.<br>
<br>
You can define the first two levels of the URI hierarchy by the controller name and method name.<br>
<br>
<br>

## When the request is not GET, a different controller will be called.<br>

### When accessing http://example.com/foo with POST method<br>
app/Controllers/api/FooApiController.php will open.<br>
FooApiController will be instantiated and the index method will be executed.<br>
If the file does not exist, a 404 status code and JSON format response will be returned.<br>
<br>
<br>
___
<br>

### http://example.com/ にアクセスが来た場合<br>
デフォルトの app/Controllers/Pages/IndexPageController.php が開きます。<br>
IndexPageController がインスタンス化されて、indexメソッドが実行されます。<br>
これはデフォルトで用意されているトップページのコントローラーです。<br>
<br>
<br>

### http://example.com/foo にアクセスが来た場合<br>
app/Controllers/Pages ディレクトリに FooPageController.php が存在すれば読み込みます。<br>
FooPageController がインスタンス化されて、indexメソッドが実行されます。<br>
もしファイルが存在しない場合、404エラーが返ります。<br>
<br>
<br>

### http://example.com/foo/bar にアクセスが来た場合<br>
FooPageController.php が開かれ、barメソッドが実装されていれば、実行されます。<br>
二つ目のパスがある場合は、indexではなく二つ目のパス名と同じ名前のメソッドが実行されます。<br>
もしメソッドが存在しない場合、404エラーが返ります。<br>
もちろん FooPageController.php が存在しない場合も404エラーが返ります。<br>
<br>
二つ目までのURI階層を、コントローラー名とメソッド名で定義することができます。<br>
<br>
<br>

## リクエストがGET以外の場合、呼び出されるコントローラーが変わります。<br>

### http://example.com/foo にPOSTメソッドでアクセスが来た場合<br>
app/Controllers/api/FooApiController.php が開かれます。<br>
FooApiController がインスタンス化されて、indexメソッドが実行されます。<br>
もしファイルが存在しない場合、404のステータスコードと、JSON形式で response が返ります。<br>
<br>
<br>
