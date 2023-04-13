<?php

namespace Error;

class ErrorPage
{
    public string|null $githubUrl = null;
    private string $hiddenDir = '';

    private string $THROW_LINE_PATTERN = '/in.+html\/(.+)\(\d+\)/';
    private string $PHP_ERROR_LINE_PATTERN = '/\/html\/(.*) on line (\d+)/';
    private string $STACKTRACE_FILE_PATH_PATTERN = '/(#\d+) .+html\/(.+)\(\d+\)/';
    private string $LINE_NUMBER_PATTERN = '/\.php\((\d+)\)/';

    private string $detailsMessage = '';

    private string|false $phpErrorLineFilePath = false;
    private string|false $phpErrorLineNum = false;
    private string $thorwLineNum = '';
    private array $lineNums = [];

    public function __construct()
    {
        $flagName = 'EXCEPTION_HANDLER_DISPLAY_GITHUB_URL';
        if (defined($flagName) && is_string($url = constant($flagName))) {
            $this->githubUrl = $url;
        } else {
            return;
        }

        $flagName = 'EXCEPTION_HANDLER_DISPLAY_DOCUMENT_ROOT_NAME';
        if (defined($flagName) && is_string($dir = constant($flagName))) {
            $this->THROW_LINE_PATTERN = "/in.+{$dir}\/(.+)\(\d+\)/";
            $this->PHP_ERROR_LINE_PATTERN = "/\/{$dir}\/(.*) on line (\d+)/";
            $this->STACKTRACE_FILE_PATH_PATTERN = "/(#\d+) .+{$dir}\/(.+)\(\d+\)/";
            $this->LINE_NUMBER_PATTERN = "/\.php\((\d+)\)/";
        }

        $flagName = 'EXCEPTION_HANDLER_DISPLAY_HIDE_DRECTORY';
        if (defined($flagName) && is_string($dir = constant($flagName))) {
            $this->hiddenDir = $dir;
        }
    }

    public function setMessage(string $detailsMessage)
    {
        $this->detailsMessage = $detailsMessage;

        if (!$this->githubUrl) {
            return;
        }

        [$this->phpErrorLineFilePath, $this->phpErrorLineNum] = $this->extractPhpErrorLine();

        $lineNums = $this->extractPhpLineNumbers();
        $this->thorwLineNum = array_shift($lineNums);
        $this->lineNums = $lineNums;
    }

    private function extractPhpErrorLine()
    {
        if (preg_match($this->PHP_ERROR_LINE_PATTERN, $this->detailsMessage, $matches)) {
            $file_path = $matches[1] ?? null;
            $line_number = $matches[2] ?? null;
        }

        return [$file_path ?? false, $line_number ?? false];
    }

    private function extractPhpLineNumbers(): array
    {
        preg_match_all($this->LINE_NUMBER_PATTERN, $this->detailsMessage, $matche);
        return $matche[1] ?? ['', ''];
    }

    public function getMessage()
    {
        return str_replace($this->hiddenDir, '', $this->detailsMessage);
    }

    public function getGithubUrlWithPhpErrorLine(): string
    {
        if ($this->phpErrorLineFilePath) {
            return $this->getGithubUrl($this->phpErrorLineFilePath, $this->phpErrorLineNum);
        } else {
            return '';
        }
    }

    public function getGithubUrlWithThrownLine(): string
    {
        return $this->getGithubURL($this->extractThrowLine(), $this->thorwLineNum);
    }

    public function getGithubUrlsWithLine(): array
    {
        $array = [];
        foreach ($this->extractPaths() as $key => $path) {
            $array[$key] = $this->getGithubURL($path, $this->lineNums[$key] ?? '');
        }

        return $array;
    }

    private function getGithubUrl(string $path, $lineNum): string
    {
        return $this->githubUrl ? ($this->githubUrl . $path . '#L' . ($lineNum ?? '')) : '';
    }

    private function extractThrowLine()
    {
        preg_match($this->THROW_LINE_PATTERN, $this->detailsMessage, $matche);
        return $matche[1] ?? '';
    }

    private function extractPaths(): array
    {
        preg_match_all($this->STACKTRACE_FILE_PATH_PATTERN, $this->detailsMessage, $matches);
        return $matches[2] ?? [];
    }

    public static function getDomainAndHttpHost(): string
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        return $protocol . '://' . ($_SERVER['HTTP_HOST'] ?? '');
    }
}

if ($detailsMessage) {
    $m = new ErrorPage;
    $m->setMessage($detailsMessage);

    $errorMessage = $m->getMessage();
    $errorLineUrl = $m->getGithubUrlWithPhpErrorLine();
    $thrownLineUrl = $m->getGithubUrlWithThrownLine();
    $linesUrl = $m->getGithubUrlsWithLine();
}

$siteUrl = ErrorPage::getDomainAndHttpHost();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/assets/favicon.png">
    <link rel="stylesheet" href="/assets/mvp.css">
    <title><?php echo "{$httpCode} {$httpStatusMessage}" ?></title>
</head>

<body>
    <style>
        h1 {
            font-size: 5rem;
        }

        main {
            margin: -3rem auto;
            margin-top: -5rem;
        }

        code {
            word-wrap: break-word;
        }

        ol {
            width: fit-content;
            margin: 0 auto;
            margin-top: 1.5rem;
            padding: 0 1rem;
        }

        a {
            word-break: break-all;
        }
    </style>
    <header>
        <nav>
            <a href="<?php echo $siteUrl ?>"><?php echo $siteUrl ?></a>
        </nav>
        <h1><?php echo $httpCode ?></h1>
        <h2><?php echo $httpStatusMessage ?></h2>
        <br>
        <p>The page you are looking for may be temporarily unavailable or may have been moved or deleted..!</p>
        <hr>
    </header>
    <main>
        <?php if ($detailsMessage) : ?>
            <section>
                <pre><code><?php echo $errorMessage ?></code></pre>
            </section>

            <?php if ($errorLineUrl || $thrownLineUrl || $linesUrl) : ?>
                <ol>
                    <!-- Error line -->
                    <?php if ($errorLineUrl) : ?>
                        <li style="list-style-type: none">
                            <small>
                                <a href="<?php echo $errorLineUrl ?>"><?php echo $errorLineUrl ?></a>
                            </small>
                        </li>
                    <?php endif ?>

                    <!-- Line -->
                    <?php if ($thrownLineUrl) : ?>
                        <li style="list-style-type: none">
                            <small>
                                <a href="<?php echo $thrownLineUrl ?>"><?php echo $thrownLineUrl ?></a>
                            </small>
                        </li>
                    <?php endif ?>

                    <!-- Stack Trace -->
                    <?php foreach ($linesUrl as $key => $url) : ?>
                        <li value="<?php echo $key ?>">
                            <small>
                                <a href="<?php echo $url ?>"><?php echo $url ?></a>
                            </small>
                        </li>
                    <?php endforeach ?>
                </ol>
            <?php endif ?>
        <?php else : ?>
            <p></p>
        <?php endif ?>
    </main>
    <footer>
        <a href="<?php echo $link ?? '' ?>">
            <?php echo $link ?? '' ?>
        </a>
    </footer>
</body>

</html>