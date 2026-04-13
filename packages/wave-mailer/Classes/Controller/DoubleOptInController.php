<?php

namespace Beffp\WaveMailer\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class DoubleOptInController extends ActionController
{
    protected $userRepository;
    public function injectUserRepository(\Beffp\WaveMailer\Domain\Repository\UserRepository $userRepository)

    {
        $this->userRepository = $userRepository;
    }
    public function confirmAction(string $hash = null){
        if ($hash === null) {
            return 'fehler';
        }
        $user = $this->userRepository->findOneByDoubleOptInHash($hash);
        if ($user === null) {
            return 'fehler';
        }
        $user->setConfirmed(true);
        $user->setDoubleOptInHash(null);

        $this->userRepository->update($user);
        return 'erfolgreich bestätigt';

    }


}