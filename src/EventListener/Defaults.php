<?php

namespace Rdlv\Steps\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class Defaults extends AbstractListener
{
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'request',
            KernelEvents::VIEW => 'view',
        ];
    }

    public function request(RequestEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        if (!($steps = $request->attributes->get('steps.defaults'))) {
            return;
        }

        $step = $this->state->get('step') ?? $steps[0];
        $this->state->set('step', $step);
        $request->attributes->set('_controller', $step);
    }

    public function view(ViewEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        if ($request->getMethod() !== 'POST') {
            return;
        }

        if (!($steps = $request->attributes->get('steps.defaults'))) {
            return;
        }

        if ($event->getControllerResult() !== 'steps.forward') {
            return;
        }

        $this->refresh($event);

        $current = $request->attributes->get('_controller');

        if (($index = array_search($current, $steps)) === false) {
            return;
        }

        $this->state->set('step', $steps[$index + 1] ?? $current);
    }
}