<?php

declare(strict_types=1);

namespace Beffp\WaveMailer\Controller;

use Beffp\WaveMailer\Domain\Repository\LinkclickRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Attribute\AsController;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\LinkHandling\LinkService;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\DebugUtility;

#[AsController]
final readonly class BackendController
{
    public function __construct(
        private ModuleTemplateFactory $moduleTemplateFactory,
        private PageRenderer $pageRenderer,
        private readonly PageRepository $pageRepository,
        private readonly LinkclickRepository $linkclickRepository,
        private readonly ConnectionPool $connectionPool,
        private readonly SiteFinder $siteFinder,
        private readonly LinkService $linkService,
        private readonly UriBuilder $uriBuilder,
    ) {}

    public function indexAction(ServerRequestInterface $request): ResponseInterface
    {
        $this->pageRenderer->loadJavaScriptModule('@beffp/wave-mailer/newsletter-card.js');

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('pages');
        $newsletterPages = $queryBuilder->select('uid')
            ->from('pages')
            ->where($queryBuilder->expr()->eq('doktype', 116))
            ->executeQuery()->fetchAllAssociative();

        $newsletters = [];
        foreach ($newsletterPages as $newsletter) {
            $newsletterUid = (int)$newsletter['uid'];
            $page = $this->pageRepository->getPage($newsletterUid);
            $clicks = $this->linkclickRepository->findBy(['newsletter' => $newsletterUid]);

            try {
                $site = $this->siteFinder->getSiteByPageId($newsletterUid);
            } catch (\Throwable) {
                $site = null;
            }

            // Get links from content
            $contentQueryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
            $contents = $contentQueryBuilder->select('bodytext')
                ->from('tt_content')
                ->where($contentQueryBuilder->expr()->eq('pid', $newsletterUid))
                ->executeQuery()->fetchAllAssociative();

            $links = [];
            foreach ($contents as $content) {
                if ($content['bodytext']) {
                    // Extract links using regex - looking for href attributes
                    preg_match_all('/href="([^"]+)"/i', $content['bodytext'], $matches);
                    if (!empty($matches[1])) {
                        foreach ($matches[1] as $link) {
                            // Basic normalization if needed, but for now we take it as is
                            if (!str_starts_with($link, '#') && !str_starts_with($link, 'mailto:')) {
                                $links[$link] = true;
                            }
                        }
                    }
                }
            }

            $linkStats = [];
            foreach (array_keys($links) as $link) {
                $count = 0;
                $resolvedLink = $link;
                $targetPageUid = 0;
                $backendUrl = null;
                $targetPageTitle = null;

                try {
                    $linkDetails = $this->linkService->resolve($link);
                    if ($linkDetails['type'] === 'page') {
                        $targetPageUid = (int)($linkDetails['pageuid'] ?? 0);
                        if ($targetPageUid > 0) {
                            $targetPage = $this->pageRepository->getPage($targetPageUid);
                            $targetPageTitle = $targetPage['title'] ?? null;
                            $backendUrl = (string)$this->uriBuilder->buildUriFromRoute('web_layout', ['id' => $targetPageUid]);
                            if ($site) {
                                $resolvedLink = (string)$site->generateUriForPage($targetPageUid);
                            }
                        }
                    }
                } catch (\Throwable) {
                    // Fallback to original link if resolution fails
                }

                /** @var \Beffp\WaveMailer\Domain\Model\Linkclick $click */
                foreach ($clicks as $click) {
                    // Match by targetPid for internal links, or by URL/Path for others
                    if ($targetPageUid > 0 && $click->getTargetPid() === $targetPageUid) {
                        $count++;
                    } elseif ($click->getLink() === $link || str_ends_with($link, $click->getLink())) {
                        $count++;
                    }
                }
                $linkStats[] = [
                    'url' => $resolvedLink,
                    'backendUrl' => $backendUrl,
                    'count' => $count,
                    'title' => $targetPageTitle
                ];
            }

            $newsletters[$newsletterUid] = [
                'page' => $page,
                'linkStats' => $linkStats,
                'totalClicks' => count($clicks)
            ];
        }


        $view = $this->moduleTemplateFactory->create($request);
        $view->assign('newsletters', $newsletters);
        return $view->renderResponse('Backend/Index');
    }
}
