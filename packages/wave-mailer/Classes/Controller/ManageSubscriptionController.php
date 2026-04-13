<?php

namespace Beffp\WaveMailer\Controller;

use Beffp\WaveMailer\Domain\Model\Subscriber;
use Beffp\WaveMailer\Domain\Model\SubscriptionGroup;
use Beffp\WaveMailer\Domain\Repository\SubscriberRepository;
use Beffp\WaveMailer\Domain\Repository\SubscriptionGroupRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class ManageSubscriptionController extends ActionController
{
    public function __construct(
        private readonly SubscriberRepository $subscriberRepository,
        private readonly SubscriptionGroupRepository $subscriptionGroupRepository,
    )
    {
    }

    public function indexAction(?Subscriber $subscriber = null): ResponseInterface
    {
        $checkedGroups = [];
        if ($subscriber !== null) {
            foreach ($subscriber->getSubscriptionGroups() as $g) {
                $checkedGroups[$g->getUid()] = true;
            }
        }

        $this->view->assignMultiple([
            'subscriber' => $subscriber,
            'allSubscriptionGroups' => $this->subscriptionGroupRepository->findAll(),
            'checkedGroups' => $checkedGroups,
        ]);

        return $this->htmlResponse();
    }

    /**
     * @throws UnknownObjectException
     * @throws IllegalObjectTypeException
     */
    public function updateAction(string $email, array $subscriptionGroups = []): ResponseInterface
    {
        /** @var Subscriber|null $subscriber */
        $subscriber = $this->subscriberRepository->findOneBy([
            'email' => $email,
        ]);

        if ($subscriber === null) {
            return $this->htmlResponse('Subscriber not found.')->withStatus(404);
        }

        $newSubscriptionGroups = new ObjectStorage();
        foreach ($subscriptionGroups as $groupName) {
            $group = $this->subscriptionGroupRepository->findOneBy(['name' => $groupName]);
            if ($group !== null) {
                $newSubscriptionGroups->attach($group);
            }
        }

        $subscriber->setSubscriptionGroups($newSubscriptionGroups);
        $this->subscriberRepository->update($subscriber);

        return $this->htmlResponse();
    }

    /**
     * @throws UnknownObjectException
     * @throws IllegalObjectTypeException
     */
    public function unsubscribeAction(string $email): ResponseInterface
    {
        /** @var Subscriber|null $subscriber */
        $subscriber = $this->subscriberRepository->findOneBy([
            'email' => $email,
        ]);

        if ($subscriber === null) {
            return $this->htmlResponse('Subscriber not found.')->withStatus(404);
        }

        $subscriber->setCancellationDate(new \DateTime());
        $this->subscriberRepository->update($subscriber);

        return $this->htmlResponse();
    }
}
