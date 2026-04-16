<?php

declare(strict_types=1);

namespace Beffp\WaveMailer\Service;

use Psr\Http\Client\ClientExceptionInterface;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Routing\InvalidRouteArgumentsException;
use TYPO3\CMS\Core\Site\SiteFinder;

/**
 * Service for rendering newsletter pages as HTML content.
 *
 * This service fetches the rendered HTML of a TYPO3 page by making
 * an internal HTTP request to the page URL.
 */
class NewsletterPageRenderer
{
    public function __construct(
        private readonly SiteFinder $siteFinder,
        private readonly RequestFactory $requestFactory,
    ) {}

    /**
     * @param int $pageUid
     * @return string
     * @throws \RuntimeException
     */
    public function render(int $pageUid): string
    {
        try {
            $site = $this->siteFinder->getSiteByPageId($pageUid);
        } catch (SiteNotFoundException $e) {
            throw new \RuntimeException(
                sprintf('Could not find site configuration for page %d: %s', $pageUid, $e->getMessage()),
                1749810001,
                $e
            );
        }

        try {
            $uri = $site->getRouter()->generateUri((string)$pageUid);
        } catch (InvalidRouteArgumentsException $e) {
            throw new \RuntimeException(
                sprintf('Could not generate URL for page %d: %s', $pageUid, $e->getMessage()),
                1749810002,
                $e
            );
        }

        $url = (string)$uri;
        if (!str_contains($url, '://')) {
            $base = rtrim((string)$site->getBase(), '/');
            if (!str_contains($base, '://')) {
                throw new \RuntimeException(
                    sprintf('Site base for page %d has no host configured. Set a full base URL in the site configuration.', $pageUid),
                    1749810006
                );
            }
            $url = $base . '/' . ltrim($url, '/');
        }

        try {
            $response = $this->requestFactory->request($url, 'GET', [
                'headers' => [
                    'Accept' => 'text/html',
                ],
            ]);
        } catch (ClientExceptionInterface $e) {
            throw new \RuntimeException(
                sprintf('HTTP request failed for page %d (%s): %s', $pageUid, $url, $e->getMessage()),
                1749810003,
                $e
            );
        }

        $statusCode = $response->getStatusCode();
        if ($statusCode !== 200) {
            throw new \RuntimeException(
                sprintf('Failed to render page %d: HTTP status %d', $pageUid, $statusCode),
                1749810004
            );
        }

        $content = $response->getBody()->getContents();

        if (empty($content)) {
            throw new \RuntimeException(
                sprintf('Rendered content for page %d is empty', $pageUid),
                1749810005
            );
        }

        return $this->makeUrlsAbsolute($content, (string)$site->getBase());
    }

    private function makeUrlsAbsolute(string $html, string $baseUrl): string
    {
        $baseUrl = rtrim($baseUrl, '/');

        return str_replace(
            ['src="/', 'href="/', "src='/", "href='/"],
            ['src="' . $baseUrl . '/', 'href="' . $baseUrl . '/', "src='" . $baseUrl . '/', "href='" . $baseUrl . '/'],
            $html
        );
    }
}
