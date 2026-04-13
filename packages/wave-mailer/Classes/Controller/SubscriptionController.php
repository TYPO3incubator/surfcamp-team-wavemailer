<?php

namespace Beffp\WaveMailer\Controller;

use Psr\Http\Message\ResponseInterface;

class SubscriptionController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    public function formAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }
}