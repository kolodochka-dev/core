<?php

namespace LindenCMS\Core;

abstract class NodeValue extends Node
{
    abstract public function set(mixed $value);

    abstract public function get(): mixed;

    public function fill(mixed $data): static
    {
        $this->set($data);

        return $this;
    }
}