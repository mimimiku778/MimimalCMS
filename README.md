# MimimalCMS
This is a super simple and minimal CMS.

The first official release version of MimimalCMS is now available, and this will be the final version of the most primitive version that does not use DI container.

## Features include:
- A micro-framework based on the MVC model
- Dynamic and static routing of controllers
- Easy definition of validation classes for string, numeric, and file format/size parameters during routing
- Middleware loading
- Basic functionality for building RESTful APIs with just a few lines of code
- Input/output and helper function calls for cookies, sessions, and flash sessions
- A wrapper class for PDO
- A string encryption class using HKDF hash and AES
- A class for image validation, automatic resizing, and saving
- Basic exception handler
- Built-in middleware for automatically managing CSRF tokens
- A simple View class consisting of output buffer control and sanitization features

<br>
<br>

## How to Use<br>
① Create a file named `PageName`PageController.php in the controllers/pages directory.<br>
<br>
That's it! You can now display it from http://example.com/ `pagename`. No special configuration is required other than implementing the page.<br>
<br>

### Points<br>
① Implement Class `PageName`PageController in controllers/pages/`PageName`PageController.php.<br>
② Inherit from AbstractPageController.<br>
③ Implement the index method.<br>
```
controllers/pages/IndexPageController.php

class IndexPageController extends AbstractPageController
{
    public function index()
    {
        echo 'Hello World';
    }
}
```
AbstractPageController is a base class for controllers that display pages.<br>
It implements common processes used to display pages.<br>
<br>
<br>
___

## 使い方<br>
① controllers/pages ディレクトリに `ページ名`PageController.php ファイルを作成する。<br>
<br>
これだけで http://example.com/ `ページ名` から表示できます。<br>
ページの実装以外に特別な設定は不要です。<br>
<br>

### ポイント<br>
①controllers/pages/`ページ名`PageController.php に、 Class `ページ名`PageController を実装する。<br>
②AbstractPageControllerを継承する。<br>
③indexメソッドを実装する。<br>
```
controllers/pages/IndexPageController.php

class IndexPageController extends AbstractPageController
{
    public function index()
     echo 'Hello World';
    }
}
```
AbstractPageController はページを表示するコントローラーの基底クラスです。<br>
ページの表示に使う共通処理が実装されています。<br>
<br>
<br>
___
<br>

### When accessing http://example.com/<br>
The default controllers/pages/IndexPageController.php will open.<br>
IndexPageController will be instantiated and the index method will be executed.<br>
This is the controller for the default top page.<br>
<br>
<br>

### When accessing http://example.com/foo<br>
If controllers/pages/FooPageController.php exists, it will be loaded.<br>
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

### When accessing http://example.com/foo/bar/hoge<br>
Even if FooPageController.php exists, a 404 error will be returned.<br>
Since it does not support the third level, a 404 error will always be returned if there is a third level.<br>
<br>
<br>

### When accessing http://example.com/foo/bar?q=hoge<br>
You can retrieve the value from $_GET['q'] in the bar method of FooPageController.php.<br>
<br>
<br>

## When the request is not GET, a different controller will be called.<br>

### When accessing http://example.com/foo with POST method<br>
controllers/api/FooApiController.php will open.<br>
FooApiController will be instantiated and the index method will be executed.<br>
The base class will be a different controller named AbstractApiController.<br>
If the file does not exist, a 404 status code and JSON format { "error": "Not Found" } will be returned.<br>
<br>
<br>
___
<br>

### http://example.com/ にアクセスが来た場合<br>
デフォルトの controllers/pages/IndexPageController.php が開きます。<br>
IndexPageController がインスタンス化されて、indexメソッドが実行されます。<br>
これはデフォルトで用意されているトップページのコントローラーです。<br>
<br>
<br>

### http://example.com/foo にアクセスが来た場合<br>
controllers/pages ディレクトリに FooPageController.php が存在すれば読み込みます。<br>
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
このように二つ目までのURI階層を、<br>
コントローラー名とメソッド名で定義することができます。<br>
<br>
<br>

### http://example.com/foo/bar/hoge にアクセスが来た場合<br>
FooPageController.php が存在していても404エラーが返ります。<br>
三つ目の階層には対応していないので、三つ目の階層がある場合は必ず404エラーが返ります。<br>
<br>
<br>

### http://example.com/foo/bar?q=hoge にアクセスが来た場合<br>
FooPageController.php のbarメソッド内で$_GET['q']から値を取得できます。<br>
<br>
<br>

## リクエストがGET以外の場合、呼び出されるコントローラーが変わります。<br>

### http://example.com/foo にPOSTメソッドでアクセスが来た場合<br>
controllers/api/FooApiController.php が開かれます。<br>
FooApiController がインスタンス化されて、indexメソッドが実行されます。<br>
基底クラスは AbstractApiController という異なるコントローラーになります。<br>
もしファイルが存在しない場合、404のステータスコードと、JSON形式で { "error": "Not Found" } が返ります。<br>
<br>
<br>

*https://example.com/*
  `Route::run();`
  new IndexPageController();
  IndexPageController::index();

 If there is only one path, calls `index()`. `index()` is implemented by default as part of the base.
*https://example.com/about*
  `Route::run();` 
  new AboutPageController();
  AboutPageController::index();

 The first path corresponds to the controller name, and the second path corresponds to the method.
*https://example.com/categories/news*
  `Route::run();` 
  new CategoriesPageController();
  AboutPageController::News();

Since it does not support the third level, a 404 error will be returned if there is a third level.
*https://example.com/categories/news/article*
  `Route::run();` 
  throw new NotFoundException;


### NOTE: Gets any path as a GET value by passing a path with placeholders as an array
 *https://example.com/blog/1234*
    `Route::run(['blog/{id}']);` 
    $_GET['id'] = 1234;
    new BlogPageController();
    BlogPageController::index();

 *https://example.com/blog/1234/aritcle*
    `Route::run(['blog/{id}']);`
    throw new NotFoundException;

 *https://example.com/blog/1234/aritcle*
    `Route::run(['blog/{id}', 'blog/{id}'/article]);`
    $_GET['id'] = 1234;
    new BlogPageController();
    BlogPageController::aritcle();

 *https://example.com/user/profile/1234*
    `Route::run(['user/profile/{userId}']);`
    $_GET['userId'] = 1234;
    new UserPageController();
    UserPageController::profile();

### NOTE: If there are three or more actual paths, a 404 error will always occur.
 *https://example.com/posts/1234/user/image*
`   Route::run(['posts/{postId}/user/image']);`
    throw new NotFoundException;
