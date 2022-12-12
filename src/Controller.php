<?php

namespace Rdlv\Steps;

use Symfony\Component\HttpFoundation\Response;

class Controller
{
    private Progress $progress;

    public function next(string $step = null): self
    {
        // dispatch next
        return $this;
    }

    public function previous(): self
    {
        // dispatch event previous
        return $this;
    }

    public function jump(string $step): self
    {
        // dispatch jump
        return $this;
    }

    public function run(): Response
    {
        // dispatch init
        return new Response();
    }

    /**
     * @param Progress $progress
     * @required
     */
    public function setProgress(Progress $progress): void
    {
        $this->progress = $progress;
    }
}