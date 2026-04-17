<?php

declare(strict_types=1);

namespace Beffp\WaveMailer\Command;

use Beffp\WaveMailer\Domain\Model\Subscriber;
use Beffp\WaveMailer\Domain\Repository\SubscriberRepository;
use Psr\Log\LoggerInterface;
use Random\RandomException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

#[AsCommand(
    name: 'wavemailer:anonymize-subscribers',
    description: 'Anonymize cancelled subscribers.',
)]
class AnonymizeSubscribersCommand extends Command
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly SubscriberRepository $subscriberRepository,
        private readonly PersistenceManager $persistenceManager,
    )
    {
        parent::__construct();
    }

    public function configure(): void
    {
        $this->addArgument('days', InputOption::VALUE_REQUIRED, 'Days after which cancelled subscribers will be anonymized.', 30);
    }

    /**
     * @throws RandomException
     * @throws InvalidQueryException
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $subscribers = $this->subscriberRepository->findCancelled((int) $input->getArgument('days'));

        /** @var Subscriber $subscriber */
        foreach ($subscribers as $subscriber) {
            $firstName = bin2hex(random_bytes(10));
            $lastName = bin2hex(random_bytes(10));
            $localPart = bin2hex(random_bytes(10));
            $domain = bin2hex(random_bytes(10));

            $subscriber->setFirstName($firstName);
            $subscriber->setLastName($lastName);
            $subscriber->setEmail($localPart . '@' . $domain . '.com');
            try {
                $this->subscriberRepository->update($subscriber);
                $this->persistenceManager->persistAll();
            } catch (IllegalObjectTypeException|UnknownObjectException $e) {
                $this->logger->error('Could not anonymize user with uid ' . $subscriber->getUid(), ['error' => $e->getMessage()]);
                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }
}
