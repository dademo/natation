<?php

namespace NatationBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Console\ConsoleEvents;

use Symfony\Component\HttpKernel\Kernel;

use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\YamlFileLoader;

class TranslationFixer implements EventSubscriberInterface
{
    private $kernel;

    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        return;
    }

    public function onConsoleCommand()
    {
        return;
    }

    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return array(
            KernelEvents::REQUEST => array(
                'TranslationAddLoaders_Reponse'
            ),
            ConsoleEvents::COMMAND => array(
                'TranslationAddLoaders_Console'
            )
        );
    }

    public function TranslationAddLoaders_Reponse(GetResponseEvent $event)
    {
        //$this->getContainer()->get('translation.writer')
        $translator = $this->kernel->getContainer()->get('translation.reader');
        $translator->addLoader('yaml', new YamlFileLoader());
        $translator->addLoader('yml', new YamlFileLoader());
        //var_dump($translator);
    }

    public function TranslationAddLoaders_Console(ConsoleCommandEvent $event)
    {
        //$this->getContainer()->get('translation.writer')
        $translator = $this->kernel->getContainer()->get('translation.reader');
        $translator->addLoader('yaml', new YamlFileLoader());
        $translator->addLoader('yml', new YamlFileLoader());
        //var_dump($translator);
    }
}