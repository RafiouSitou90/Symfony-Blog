<?php

namespace App\Traits;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

trait EmailAddressTrait
{
    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $parameterBag;

    /**
     * EmailAddress constructor.
     *
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct (ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }


    /**
     * @return string
     */
    private function getNoReplyAddress (): string
    {
        return $this->parameterBag->get('app.email.no_reply');
    }
}
