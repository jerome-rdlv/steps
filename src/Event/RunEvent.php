<?php

namespace Rdlv\Steps\Event;

/**
 *
 */
class RunEvent extends StepEvent
{
    /** @var object step service controller */
    private $step;

    /**
     * @return object
     */
    public function getStep()
    {
        return $this->step;
    }
}