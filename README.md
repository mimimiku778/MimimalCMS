# MimimalCMS
This is a super simple, minimal CMS.

使い方はとても簡単です。

controllersフォルダに `ページ名`PageController.php を作れば
http://example.com/`ページ名` でそのページが開きます。
設定不要！

`ページ名`PageController.php の中には一つのクラスだけを書きます。
クラス名は `ページ名`PageController にします。

そこで重要なポイントが２つあります。
①AbstractPageControllerを継承する。
②indexメソッドを必ず実装する。

例
    class IndexPageController extends AbstractPageController
    {
        public function index()
        {
            echo 'Hello World';
        }
    }

AbstractPageControllerはページを表示するコントローラーの基底クラスです。
ページの表示に使う共通の処理が実装されています。


http://example.com/ にアクセスが来た場合
デフォルトの controllers/pages/IndexPageController.php が開きます。
IndexPageControllerがインスタンス化されて、indexメソッドが実行されます。
これはデフォルトで用意されているトップページのコントローラーです。

http://example.com/foo にアクセスが来た場合
controllers/pages フォルダに FooPageController.php が存在すればファイルが開かれます。
FooPageControllerがインスタンス化されて、indexメソッドが実行されます。
もしファイルが存在しない場合、404エラーが返ります。

例えば、
http://example.com/foo/bar にアクセスが来た場合
FooPageController.php が開かれ、barメソッドが実装されていれば、実行されます。
二つ目のパスがある場合は、indexではなく二つ目のパス名のメソッドが実行されます。
もしメソッドが存在しない場合、404エラーが返ります。
もちろん FooPageController.php が存在しない場合も404エラーです。

このように二つ目のパスまでの階層を、
コントローラー名とメソッド名で定義することができます。

http://example.com/foo/bar/hoge にアクセスが来た場合
FooPageController.php が存在していても404エラーが返ります。
三つ目の階層には対応していないので、三つ目の階層がある場合は必ず404エラーが返ります。

http://example.com/foo/bar?q=hoge にアクセスが来た場合
FooPageController.php のbarメソッド内で$_GET['q']から値を取得できます。


リクエストがPOSTの場合に限り、呼び出されるコントローラーが変わります。

http://example.com/foo にPOSTメソッドでアクセスが来た場合
controllers/api/FooApiController.php が開かれます。
FooApiControllerがインスタンス化されて、indexメソッドが実行されます。
基底クラスは AbstractApiController という異なるコントローラーになります。


まだ作成中です！