<?php

namespace Rdlv\Steps\EventListener;

use Exception;
use Rdlv\Steps\Event\ForwardEvent;
use Rdlv\Steps\Event\InitEvent;
use Rdlv\Steps\State;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class KernelListener implements EventSubscriberInterface
{
    public const MOVE_FORWARD = 'steps.forward';

    private const ROUTE_INDEX = 'steps.index';
    private const ROUTE_BACK = 'steps.back';
    private const ROUTE_JUMP = 'steps.jump';

    private EventDispatcherInterface $dispatcher;

    private RouterInterface $router;

    private State $progress;

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

    private function refresh(): Response
    {
        return new RedirectResponse($this->router->generate(self::ROUTE_INDEX));
    }

    private function getDefaultNextStep(KernelEvent $event)
    {
        $request = $event->getRequest();
        $steps = $request->attributes->get('steps');
        if (!$steps) {
            return null;
        }

        if (($current = $this->progress->step()) === null) {
            // get first
            return $steps[0];
        }

        // get next
        if (($index = array_search($current, $steps)) === false) {
            return null;
        }

        return $steps[$index + 1] ?? null;
    }

    public function request(RequestEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        /*
         * if steps enabled in request attributes, override the controller
         *  - dispatch a select event (what step to display) : get a step id
         *  - dispatch a do event (apply the change) : Response|null
         *  - if Response, set it on $event, otherwise set step as controller
         */

        $request = $event->getRequest();
        switch ($request->attributes->get('_route')) {
            case self::ROUTE_INDEX:
                if ($this->progress->step() === null) {
                    // progress initialization
                    $forwardEvent = new ForwardEvent($this->progress, $this->getDefaultNextStep($event));
                    $this->dispatcher->dispatch($forwardEvent);

                    $this->progress->forward($forwardEvent->step);
                    $request->attributes->set('_controller', $this->progress->step());
                    return;
                } else {
                    $initEvent = new InitEvent($this->progress);
                    $this->dispatcher->dispatch($initEvent);

                    if (!$initEvent->step) {
                        // should not happen as init event is dispatched with a default value
                        throw new Exception(self::class . ' got an empty step value after init event.');
                    }

                    if ($initEvent->step === $this->progress->step()) {
                        $request->attributes->set('_controller', $this->progress->step());
                        return;
                    }

                    $this->progress->jump($initEvent->step);
                    $event->setResponse($this->refresh());
                    return;
                }
            case self::ROUTE_BACK:
                $this->progress->back();
                $event->setResponse($this->refresh());
                return;
            case self::ROUTE_JUMP:
                $this->progress->jump($request->attributes->get('step'));
                $event->setResponse($this->refresh());
                return;
        }
    }

    public function view(ViewEvent $event)
    {
        if ($event->getControllerResult() !== self::MOVE_FORWARD) {
            return;
        }

        $forwardEvent = new ForwardEvent($this->progress, $this->getDefaultNextStep($event));
        $this->dispatcher->dispatch($forwardEvent);

        if (!$forwardEvent->step) {
            return;
        }

        $this->progress->forward($forwardEvent->step);
        $event->setResponse($this->refresh());
    }

    /**
     * @param EventDispatcherInterface $dispatcher
     * @required
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher): void
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param RouterInterface $router
     * @required
     */
    public function setRouter(RouterInterface $router): void
    {
        $this->router = $router;
    }

    /**
     * @param State $state
     * @required
     */
    public function setState(State $state): void
    {
        $this->progress = $state;
    }
}