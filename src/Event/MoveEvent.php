<?php

namespace Rdlv\Steps\Event;

use Rdlv\Steps\State;

class MoveEvent extends StepEvent
{
    public ?string $step = null;

    public function __construct(State $progress, string $step = null)
    {
        parent::__construct($progress);
        $this->step = $step;
    }
}