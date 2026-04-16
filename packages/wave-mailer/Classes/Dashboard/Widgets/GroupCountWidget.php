<?php

declare(strict_types=1);

namespace Beffp\WaveMailer\Dashboard\Widgets;

use Beffp\WaveMailer\Domain\Repository\SubscriptionGroupRepository;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\View\BackendViewFactory;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Dashboard\Widgets\ButtonProviderInterface;
use TYPO3\CMS\Dashboard\Widgets\RequestAwareWidgetInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;

class GroupCountWidget implements WidgetInterface, RequestAwareWidgetInterface
{
    private ServerRequestInterface $request;

    public function __construct(
        private readonly WidgetConfigurationInterface $configuration,
        private readonly BackendViewFactory $backendViewFactory,
        private readonly SubscriptionGroupRepository $subscriptionGroupRepository,
        private readonly ConnectionPool $connectionPool,
        private readonly ?ButtonProviderInterface $buttonProvider = null,
        private readonly array $options = []

    ) {
    }

    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    public function renderWidgetContent(): string
    {
        $view = $this->backendViewFactory->create($this->request);
        $view->assignMultiple([
            'groups' => $this->getSubscriberCounts(),
            'options' => $this->options,
            'configuration' => $this->configuration,
        ]);

        return $view->render('Widget/GroupCountWidget');
    }

    protected function getSubscriberCounts(): array
    {
        $subscriberGroups = $this->subscriptionGroupRepository->findAll();
        return [...$subscriberGroups];
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}