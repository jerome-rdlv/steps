<?php

namespace Rdlv\Steps\EventListener;

use Rdlv\Steps\State;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\Routing\RouterInterface;

abstract class AbstractListener implements EventSubscriberInterface
{
    protected State $state;
    protected RouterInterface $router;

    protected function refresh(KernelEvent $event)
    {
        $event->setResponse(
            new RedirectResponse(
                $this->router->generate($event->getRequest()->attributes->get('_route'))
            )
        );
    }

    /**
     * @param State $state
     * @required
     */
    public function setState(State $state): void
    {
        $this->state = $state;
    }

    /**
     * @param RouterInterface $router
     * @required
     */
    public function setRouter(RouterInterface $router): void
    {
        $this->router = $router;
    }
}