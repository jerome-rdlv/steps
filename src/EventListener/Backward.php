<?php

namespace Rdlv\Steps\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class Backward extends AbstractListener
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
        if ($request->getMethod() !== 'POST' || !$request->request->has('steps.back')) {
            return;
        }

        // going back
        $this->refresh($event);

        if (!($previous = $this->state->get('previous'))) {
            return;
        }

        // found previous, update state
        $this->state->set('step', array_pop($previous));
        $this->state->set('previous', $previous);
    }

    public function view(ViewEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        if ($request->getMethod() !== 'POST' || !$request->attributes->get('steps.back')) {
            return;
        }

        $previous = $this->state->get('previous') ?? [];
        $current = $request->attributes->get('_controller');
        if (($index = array_search(get_class($current), $previous)) !== false) {
            array_splice($previous, $index);
        }
        array_push($previous, $current);
        $this->state->set('previous', $previous);
    }
}