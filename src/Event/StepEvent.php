<?php

namespace Rdlv\Steps\Event;

use Rdlv\Steps\State;
use Symfony\Contracts\EventDispatcher\Event;

abstract class StepEvent extends Event
{
    public State $progress;

    public function __construct(State $progress)
    {
        // prevent progress modification
        $this->progress = clone $progress;
    }
}