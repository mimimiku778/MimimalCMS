<?php

namespace Shared\Exceptions;

use App\Config\AppConfig;
use Shared\MimimalCmsConfig;

/**
 * ErrorPage class to handle displaying error message and generating Github URLs for error lines
 */
class ErrorPage
{
    /**
     * @var string|null The Github repository URL to generate links for error lines
     */
    public string|null $githubUrl = null;

    /**
     * @var string The directory name to be hidden in error messages
     */
    private string $hiddenDir = '';

    /**
     * @var string The regex pattern to extract the throw line number from error message
     */
    private string $THROW_LINE_PATTERN = '/in.+html\/(.+)\(\d+\)/';

    /**
     * @var string The regex pattern to extract the PHP error line number and file path from error message
     */
    private string $PHP_ERROR_LINE_PATTERN = '/\/html\/(.*) on line (\d+)/';

    /**
     * @var string The regex pattern to extract the file path and line number from error stack trace
     */
    private string $STACKTRACE_FILE_PATH_PATTERN = '/(#\d+) .+html\/(.+)\(\d+\)/';

    /**
     * @var string The regex pattern to extract the line number from PHP code in error message
     */
    private string $LINE_NUMBER_PATTERN = '/\.php\((\d+)\)/';

    /**
     * @var string The detailed error message
     */
    private string $detailsMessage = '';

    /**
     * @var string|false The PHP file path of the error line or false if not found
     */
    private string|false $phpErrorLineFilePath = false;

    /**
     * @var string|false The line number of the error line or false if not found
     */
    private string|false $phpErrorLineNum = false;

    /**
     * @var string The line number of the throw line
     */
    private string $thorwLineNum = '';

    /**
     * @var array The line numbers of the PHP code in error message
     */
    private array $lineNums = [];

    /**
     * Constructor method to initialize ErrorPage object
     */
    public function __construct()
    {
        $config = MimimalCmsConfig::class;
        if (class_exists($config) && isset($config::$errorPageGitHubUrl)) {
            $this->githubUrl = $config::$errorPageGitHubUrl;
        } else {
            return;
        }

        if (isset($config::$errorPageDocumentRootName)) {
            $dir = $config::$errorPageDocumentRootName;
            $this->THROW_LINE_PATTERN = "/in.+{$dir}\/(.+)\(\d+\)/";
            $this->PHP_ERROR_LINE_PATTERN = "/\/{$dir}\/(.*) on line (\d+)/";
            $this->STACKTRACE_FILE_PATH_PATTERN = "/(#\d+) .+{$dir}\/(.+)\(\d+\)/";
            $this->LINE_NUMBER_PATTERN = "/\.php\((\d+)\)/";
        }

        if (isset($config::$errorPageHideDirectory)) {
            $this->hiddenDir = $config::$errorPageHideDirectory;
        }
    }

    /**
     * Set the error message and extract necessary information from it
     *
     * @param string $detailsMessage The detailed error message
     */
    public function setMessage(string $detailsMessage)
    {
        $this->detailsMessage = $detailsMessage;

        if (!$this->githubUrl) {
            return;
        }

        [$this->phpErrorLineFilePath, $this->phpErrorLineNum] = $this->extractPhpErrorLine();

        $lineNums = $this->extractPhpLineNumbers();
        if (count($lineNums) > 1) {
            $this->thorwLineNum = array_shift($lineNums);
            $this->lineNums = $lineNums;
        }
    }

    /**
     * Get the error message with the hidden directory name removed
     *
     * @return string The error message
     */
    public function getMessage()
    {
        return str_replace($this->hiddenDir, '', $this->detailsMessage);
    }

    /**
     * Get the Github URL for the error line where the PHP error occurred
     *
     * @return string The Github URL for the error line or an empty string if not found
     */
    public function getGithubUrlWithPhpErrorLine(): string
    {
        if ($this->phpErrorLineFilePath) {
            return $this->getGithubUrl($this->phpErrorLineFilePath, $this->phpErrorLineNum);
        } else {
            return '';
        }
    }

    /**
     * Get the Github URL for the throw line
     *
     * @return string The Github URL for the throw line
     */
    public function getGithubUrlWithThrownLine(): string
    {
        $line = $this->extractThrowLine();
        if ($line) {
            return $this->getGithubURL($line, $this->thorwLineNum);
        } else {
            return '';
        }
    }

    /**
     * Get an array of Github URLs for all lines of PHP code in error message
     *
     * @return array An array of Github URLs
     */
    public function getGithubUrlsWithLine(): array
    {
        $array = [];
        foreach ($this->extractPaths() as $key => $path) {
            $array[$key] = $this->getGithubURL($path, $this->lineNums[$key] ?? '');
        }

        return $array;
    }

    /**
     * Get the Github URL for a given file path and line number
     *
     * @param string $path The file path of the error line
     * @param string|false $lineNum The line number of the error line or false if not found
     *
     * @return string The Github URL for the error line or an empty string if the Github URL is not set
     */
    private function getGithubUrl(string $path, $lineNum): string
    {
        return $this->githubUrl ? ($this->githubUrl . $path . '#L' . ($lineNum ?? '')) : '';
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
        $config = MimimalCmsConfig::class;
        if (class_exists($config) && isset($config::$urlRoot)) {
            $urlRoot = $config::$urlRoot;
        }

        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        return $protocol . '://' . ($_SERVER['HTTP_HOST'] ?? '') . ($urlRoot ?? '');
    }

    public static function fileUrl(string $filePath): string
    {
        $config = MimimalCmsConfig::class;
        if (!class_exists($config) || !isset($config::$publicDir)) {
            return self::getDomainAndHttpHost() . $filePath;
        }

        $filePath = "/" . ltrim($filePath, "/");
        $fullFilePath = $config::$publicDir . $filePath;

        if (!file_exists($fullFilePath)) {
            return self::getDomainAndHttpHost() . $filePath;
        }

        return self::getDomainAndHttpHost() . $filePath . '?v=' . filemtime($fullFilePath);
    }
}

try {
    if ($detailsMessage) {
        $m = new ErrorPage;
        $m->setMessage($detailsMessage);

        // Get the error message from the ErrorPage object.
        $errorMessage = $m->getMessage();

        // Get the Github URL with the PHP error line.
        $errorLineUrl = $m->getGithubUrlWithPhpErrorLine();

        // Get the Github URL with the thrown line.
        $thrownLineUrl = $m->getGithubUrlWithThrownLine();

        // Get an array of Github URLs with each line in the error message.
        $linesUrl = $m->getGithubUrlsWithLine();
    }
} catch (\Exception $e) {
    $errorMessage = $e->getMessage();
}

// Get the domain and http host of the current page using the static method getDomainAndHttpHost() of ErrorPage class.
$siteUrl = ErrorPage::getDomainAndHttpHost();

$iconUrl = '';
$config = AppConfig::class;
if (!class_exists($config) || !isset($config::$siteIconFilePath)) {
    $iconUrl = ErrorPage::fileUrl($config::$siteIconFilePath);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="<?php echo $iconUrl ?>">
    <link rel="stylesheet" href="<?php echo ErrorPage::fileUrl('assets/mvp.css') ?>">
    <title><?php echo "{$httpCode} {$httpStatusMessage}" ?></title>
</head>

<body>
    <style>
        /* Increase size of the main heading */
        h1 {
            font-size: 5rem;
        }

        /* Center and adjust the margin of the main section */
        main {
            margin: -3rem auto;
            margin-top: -5rem;
        }

        /* Break long lines in the code section */
        code {
            word-wrap: break-word;
        }

        /* Set width, center, and add padding to the ordered list */
        ol {
            width: fit-content;
            margin: 0 auto;
            margin-top: 1.5rem;
            padding: 0 1rem;
        }

        /* Break URLs to fit in the list */
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
            <!-- Display error message if it exists -->
            <section>
                <pre><code><?php echo $errorMessage ?></code></pre>
            </section>
            <?php if ($errorLineUrl || $thrownLineUrl || $linesUrl) : ?>
                <!-- Display links to relevant lines on GitHub if available -->
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
            <!-- Display empty paragraph if error message does not exist -->
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