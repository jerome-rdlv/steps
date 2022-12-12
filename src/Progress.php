<?php

class Progress
{
    public string $step;
    public array $path = [];
    public array $history = [];
    public array $previous = [];
    public array $extra = [];

    public function next(string $step): self
    {
        $this->previous[] = $this->step;
        $this->jump($step);
        return $this;
    }

    public function previous(): self
    {
        if ($previous = array_pop($this->previous)) {
            $this->jump($previous);
        }
        return $this;
    }

    public function jump(string $step): self
    {
        $this->history[date('Y-m-d H:i:s T')] = $step;
        if (!in_array($step, $this->path)) {
            $currentIndex = array_search($this->step, $this->path);
            if ($currentIndex === false) {
                // insert at the end
                $this->path[] = $step;
            } else {
                // insert right after current step
                array_splice($this->path, $currentIndex + 1, 0, $step);
            }
        }
        $this->step = $step;
        return $this;
    }

    public function hasPrevious(): bool
    {
        return !!$this->previous;
    }

    public function lock(): self
    {
        $this->path = [];
        return $this;
    }

    public function clear(): self
    {
        $this->step = null;
        $this->path = [];
        $this->history = [];
        $this->extra = [];
        return $this;
    }

    public function set(string $key, $value): self
    {
        $this->extra[$key] = $value;
        return $this;
    }

    public function get($key)
    {
        if (array_key_exists($key, $this->extra)) {
            return $this->extra[$key];
        }
        return null;
    }

    public function unset(string $key): self
    {
        if (array_key_exists($key, $this->extra)) {
            unset($this->extra[$key]);
        }
        return $this;
    }
}