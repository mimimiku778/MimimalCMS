# MimimalCMS
This is a super simple, minimal CMS.
<br>

## 使い方は簡単<br>
① controllers/pages ディレクトリに `ページ名`PageController.php ファイルを作成する。<br>
これだけで http://example.com/ `ページ名` から表示できます。<br>
### 設定不要！<br>

## ポイント<br>
①`ページ名`PageController.php に、 Class `ページ名`PageController を実装する。<br>
②AbstractPageControllerを継承する。<br>
③indexメソッドを実装する。<br>
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
AbstractPageControllerはページを表示するコントローラーの基底クラスです。<br>
ページの表示に使う共通の処理が実装されています。<br>
<br>
`ページ名`PageController.php に、 Class `ページ名`PageController を実装する。<br>
<br>
<br>

## http://example.com/ にアクセスが来た場合<br>
デフォルトの controllers/pages/IndexPageController.php が開きます。<br>
IndexPageController がインスタンス化されて、indexメソッドが実行されます。<br>
これはデフォルトで用意されているトップページのコントローラーです。<br>
<br>
<br>

## http://example.com/foo にアクセスが来た場合<br>
controllers/pages ディレクトリに FooPageController.php が存在すれば読み込みます。<br>
FooPageController がインスタンス化されて、indexメソッドが実行されます。<br>
もしファイルが存在しない場合、404エラーが返ります。<br>
<br>
<br>

## http://example.com/foo/bar にアクセスが来た場合<br>
FooPageController.php が開かれ、barメソッドが実装されていれば、実行されます。<br>
二つ目のパスがある場合は、indexではなく二つ目のパス名と同じ名前のメソッドが実行されます。<br>
もしメソッドが存在しない場合、404エラーが返ります。<br>
もちろん FooPageController.php が存在しない場合も404エラーが返ります。<br>
<br>
このように二つ目までのURI階層を、<br>
コントローラー名とメソッド名で定義することができます。<br>
<br>
<br>

## http://example.com/foo/bar/hoge にアクセスが来た場合<br>
FooPageController.php が存在していても404エラーが返ります。<br>
三つ目の階層には対応していないので、三つ目の階層がある場合は必ず404エラーが返ります。<br>
<br>
<br>

## http://example.com/foo/bar?q=hoge にアクセスが来た場合<br>
FooPageController.php のbarメソッド内で$_GET['q']から値を取得できます。<br>
<br>
<br>

# リクエストがPOSTの場合、呼び出されるコントローラーが変わります。<br>

## http://example.com/foo にPOSTメソッドでアクセスが来た場合<br>
controllers/api/FooApiController.php が開かれます。<br>
FooApiController がインスタンス化されて、indexメソッドが実行されます。<br>
基底クラスは AbstractApiController という異なるコントローラーになります。<br>
<br>
<br>
まだ作成中です！<br>
限りなくシンプルなViewのコンポーネントと定義方法を実装してます！<br>
SEOも忘れずに！<br>