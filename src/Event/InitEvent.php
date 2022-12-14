<?php

namespace Rdlv\Steps\Event;

use Rdlv\Steps\State;

/**
 * Dispatched before step instanciation. Gives a last chance to run another step.
 */
class InitEvent extends StepEvent
{
    public ?string $step;

    public function __construct(State $progress)
    {
        parent::__construct($progress);
        $this->step = $progress->step();
    }
}