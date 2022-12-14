<?php

namespace Rdlv\Steps;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ProgressStorage
{
    private string $namespace;

    private SessionInterface $session;

    public function __construct(string $namespace = '_progress')
    {
        $this->namespace = $namespace;
    }

    public function __invoke(): State
    {
        if ($progress = $this->session->get($this->namespace)) {
            return $progress;
        }
        $progress = new State();
        $this->session->set($this->namespace, $progress);
        return $progress;
    }

    /**
     * @param SessionInterface $session
     * @required
     */
    public function setSession(SessionInterface $session): void
    {
        $this->session = $session;
    }
}