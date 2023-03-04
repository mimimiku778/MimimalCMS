<?php

declare(strict_types=1);

class Route
{
    private bool $isPost;

    public function __construct()
    {
        // リクエストメソッドを取得
        $this->isPost = $_SERVER['REQUEST_METHOD'] === 'POST';

        // リクエスト URI を取得
        $requestUri = strtolower(strtok($_SERVER['REQUEST_URI'] ?? '/', '?'));

        // リクエスト URI を解析
        [$controllerClassName, $methodName] = $this->parseRequestUri($requestUri);

        // コントローラーメソッドを実行
        $this->runControllerMethod($controllerClassName, $methodName);
    }

    private function parseRequestUri(string $requestUri): array
    {
        // リクエスト URI を / で分割
        $path = explode('/', $requestUri);

        // 3番目のパスがある場合は 404 エラー
        if (isset($path[3]) && $path[3]) {
            $this->showError();
        }

        // パスに不正な文字が含まれる場合は 404 エラー
        if (preg_grep('/[^a-z0-9_]/', array_slice($path, 1))) {
            $this->showError();
        }

        // デフォルトのコントローラー名を設定
        $controllerClassName = $this->isPost ? 'IndexApiController' : 'IndexPageController';

        // コントローラー名を解決
        if (!empty($path[1])) {
            $controllerPrefix = ucfirst($path[1]);
            $controllerSuffix = $this->isPost ? 'ApiController' : 'PageController';
            $controllerClassName = $controllerPrefix . $controllerSuffix;
        }

        // メソッド名を解決
        if (isset($path[2]) && $path[2]) {
            $methodName = $path[2];
        } else {
            // 2番目のパスが空の場合はindexを使用
            $methodName = 'index';
        }

        // コントローラー名とメソッド名を返す
        return [$controllerClassName, $methodName];
    }

    private function runControllerMethod(string $controllerClassName, string $methodName)
    {
        // コントローラーのファイルパスを解決
        $controllerDir = $this->isPost ? 'api' : 'pages';
        $controllerFilePath = __DIR__ . "/../controllers/{$controllerDir}/{$controllerClassName}.php";

        // コントローラーファイルが存在しない場合は 404 エラー
        if (!file_exists($controllerFilePath)) {
            $this->showError();
        }

        // コントローラーの基底クラスを読み込み
        $controllerBaseClass = $this->isPost ? 'AbstractApiController' : 'AbstractPageController';
        require __DIR__ . "/{$controllerBaseClass}.php";

        // コントローラーを読み込み
        require $controllerFilePath;
        $controller = new $controllerClassName();

        // メソッドが存在しない場合は 404 エラー
        if (!method_exists($controller, $methodName)) {
            $this->showError();
        }

        // コントローラーメソッドを実行
        $controller->$methodName();
    }

    private function showError()
    {
        http_response_code(404);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            exit(json_encode(['error' => 'Not Found']));
        } else {
            // 404 エラーページを表示
            exit('<h1>ページが見つかりません！<h1>');
        }
    }
}
