<?php

namespace Rdlv\Steps;

class State
{
    private array $data = [];

    public function set(string $key, $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function get($key)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }
        return null;
    }

    public function unset(string $key): self
    {
        if (array_key_exists($key, $this->data)) {
            unset($this->data[$key]);
        }
        return $this;
    }
}