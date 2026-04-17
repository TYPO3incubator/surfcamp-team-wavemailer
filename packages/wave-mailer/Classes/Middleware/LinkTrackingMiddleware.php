<?php

declare(strict_types=1);

namespace Beffp\WaveMailer\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

class LinkTrackingMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly ConnectionPool $connectionPool)
    {
    }


    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
    ): ResponseInterface {
        if (isset($request->getQueryParams()['tx_wavemailer']['track']) && isset($request->getQueryParams()['tx_wavemailer']['pid'])) {
            /** @var QueryBuilder $queryBuilder */
            $queryBuilder = $this->connectionPool
                ->getQueryBuilderForTable('tx_wavemailer_domain_model_linkclick');

            $queryBuilder->insert('tx_wavemailer_domain_model_linkclick')
                ->values([
                    'newsletter' => (int)$request->getQueryParams()['tx_wavemailer']['pid'],
                    'link' => $request->getUri()->getPath(),
                    'time' => time(),
                    'target_pid' => $request->getAttribute('routing')->getPageId()
                ])
                ->executeStatement();
        }

        return $handler->handle($request);
    }
}
