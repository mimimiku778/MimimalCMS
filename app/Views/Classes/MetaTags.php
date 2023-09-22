<?php

namespace App\Views;

use App\Config\AppConfig;

/**
 * Generates metadata tags for HTML header.
 */
class MetaTags
{
    /**
     * The default OGP image URL.
     */
    private string $ogImageUrl;

    /**
     * The website name.
     */
    public string $ogSiteName = 'MimimalCMS';

    /**
     * The title of the page.
     */
    public string $title = 'MimimalCMS';

    public string $titleSeparator = ' | ';

    public string $currentTitle;

    /**
     * The description of the page.
     */
    public string $description = 'This is MimimalCMS';

    /**
     * The OGP description of the page.
     */
    public string $ogDescription;

    /**
     * The OGP locale of the page.
     */
    public string $ogLocale = 'ja_JP'; //TODO: Local

    /**
     * The OGP type of the page.
     */
    public string $ogType = 'website';


    public function __construct()
    {
        $this->ogImageUrl = fileUrl(AppConfig::DEFAULT_OGP_IMAGE_FILE_PATH);
    }

    /**
     * Magic method to cast the object to a string.
     *
     * @return string The generated metadata tags.
     */
    public function __toString(): string
    {
        return $this->generateTags();
    }

    /**
     * Sets the title of the page.
     *
     * @param string $title The title of the page.
     *
     * @return static
     */
    public function setCurrentTitle(string $title): static
    {
        $this->currentTitle = h($title);

        return $this;
    }

    /**
     * Gets the current title of the page.
     *
     * If the current title is not set, the default title will be returned.
     *
     * @return string The current title of the page.
     */
    public function getCurrentTitle(): string
    {
        return $this->currentTitle ?? $this->title;
    }

    /**
     * Sets the OGP description of the page.
     *
     * @param string $ogDescription The OGP description of the page.
     *
     * @return static
     */
    public function setOgpDescription(string $ogDescription): static
    {
        $this->ogDescription = h($ogDescription);

        return $this;
    }

    /**
     * Sets the OGP image URL of the page.
     *
     * @param string $ogImageUrl The OGP image URL of the page.
     *
     * @return static
     */
    public function setOgpImageUrl(string $ogImageUrl): static
    {
        $this->ogImageUrl = $ogImageUrl;

        return $this;
    }

    /**
     * Generates metadata tags for HTML header.
     *
     * @return string The generated metadata tags.
     */
    public function generateTags(): string
    {
        $tags = [
            '<title>' . (isset($this->currentTitle) ? ($this->currentTitle . $this->titleSeparator . $this->title) : $this->title) . '</title>',
            '<meta name="description" content="' . $this->description . '">',
        ];

        $tags = array_merge($tags, [
            '<meta property="og:locale" content="' . $this->ogLocale . '">',
            '<meta property="og:url" content="' . url(path()) . '">',
            '<meta property="og:type" content="' . $this->ogType . '">',
            '<meta property="og:title" content="' . $this->title . '">',
            '<meta property="og:description" content="' . ($this->ogDescription ?? $this->description) . '">',
            '<meta property="og:image" content="' . $this->ogImageUrl . '">',
            '<meta property="og:site_name" content="' . $this->ogSiteName . '">',
            '<meta name="twitter:card"  content="summary_large_image">',
        ]);

        return implode("\n", $tags);
    }
}
