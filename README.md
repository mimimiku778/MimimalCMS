# MimimalCMS
This is a super simple, minimal CMS.

使い方はとても簡単です。

controllersフォルダに `ページ名`PageController.php を作れば<br>
http://example.com/`ページ名` でそのページが開きます。<br>
設定不要！<br>

`ページ名`PageController.php の中には一つのクラスだけを書きます。<br>
クラス名は `ページ名`PageController にします。<br>

そこで重要なポイントが２つあります。<br>
①AbstractPageControllerを継承する。<br>
②indexメソッドを必ず実装する。<br>
<br>
例<br>
    class IndexPageController extends AbstractPageController<br>
    {<br>
        public function index()<br>
        {<br>
            echo 'Hello World';<br>
        }<br>
    }<br>
<br>
AbstractPageControllerはページを表示するコントローラーの基底クラスです。<br>
ページの表示に使う共通の処理が実装されています。<br>
<br>
<br>
http://example.com/ にアクセスが来た場合<br>
デフォルトの controllers/pages/IndexPageController.php が開きます。<br>
IndexPageControllerがインスタンス化されて、indexメソッドが実行されます。<br>
これはデフォルトで用意されているトップページのコントローラーです。<br>
<br>
http://example.com/foo にアクセスが来た場合<br>
controllers/pages フォルダに FooPageController.php が存在すればファイルが開かれます。<br>
FooPageControllerがインスタンス化されて、indexメソッドが実行されます。<br>
もしファイルが存在しない場合、404エラーが返ります。<br>
<br>
例えば、<br>
http://example.com/foo/bar にアクセスが来た場合<br>
FooPageController.php が開かれ、barメソッドが実装されていれば、実行されます。<br>
二つ目のパスがある場合は、indexではなく二つ目のパス名のメソッドが実行されます。<br>
もしメソッドが存在しない場合、404エラーが返ります。<br>
もちろん FooPageController.php が存在しない場合も404エラーです。<br>
<br>
このように二つ目のパスまでの階層を、<br>
コントローラー名とメソッド名で定義することができます。<br>
<br>
http://example.com/foo/bar/hoge にアクセスが来た場合<br>
FooPageController.php が存在していても404エラーが返ります。<br>
三つ目の階層には対応していないので、三つ目の階層がある場合は必ず404エラーが返ります。<br>
<br>
http://example.com/foo/bar?q=hoge にアクセスが来た場合<br>
FooPageController.php のbarメソッド内で$_GET['q']から値を取得できます。<br>
<br>
<br>
リクエストがPOSTの場合に限り、呼び出されるコントローラーが変わります。<br>
<br>
http://example.com/foo にPOSTメソッドでアクセスが来た場合<br>
controllers/api/FooApiController.php が開かれます。<br>
FooApiControllerがインスタンス化されて、indexメソッドが実行されます。<br>
基底クラスは AbstractApiController という異なるコントローラーになります。<br>
<br>
<br>
まだ作成中です！<br>