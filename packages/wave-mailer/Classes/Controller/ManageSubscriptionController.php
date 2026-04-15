<?php

namespace Beffp\WaveMailer\Controller;

use Beffp\WaveMailer\Domain\Model\Subscriber;
use Beffp\WaveMailer\Domain\Repository\SubscriberRepository;
use Beffp\WaveMailer\Domain\Repository\SubscriptionGroupRepository;
use Beffp\WaveMailer\Domain\Validation\SubscriberValidator;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Attribute\Validate;
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

    public function indexAction(#[Validate(validator: SubscriberValidator::class)] ?Subscriber $subscriber = null): ResponseInterface
    {
        $checkedGroups = [];
        if ($subscriber !== null) {
            foreach ($subscriber->getSubscriptionGroups() as $group) {
                $checkedGroups[$group->getUid()] = true;
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
            throw new \RuntimeException('No subscriber found for the given email address.', 1776159682);
        }

        if ($subscriber->getCancellationDate()) {
            throw new \RuntimeException('This subscription has been cancelled.', 1776159683);
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
            throw new \RuntimeException('No subscriber found for the given email address.', 1776159682);
        }

        if ($subscriber->getCancellationDate()) {
            throw new \RuntimeException('This subscription has already been cancelled.', 1776159683);
        }

        $subscriber->setCancellationDate(new \DateTime());
        $subscriber->setHidden(true);
        $this->subscriberRepository->update($subscriber);

        return $this->htmlResponse();
    }
}
