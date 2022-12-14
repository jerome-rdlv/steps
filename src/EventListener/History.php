<?php

namespace Rdlv\Steps\EventListener;

use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class History extends AbstractListener
{
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => 'view',
        ];
    }

    public function view(ViewEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        if (!$request->attributes->get('steps.history')) {
            return;
        }

        $history = $this->state->get('history') ?? [];
        $history[date('Y-m-d H:i:s T')] = $request->attributes->get('_controller');
    }
}