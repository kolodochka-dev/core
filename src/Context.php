<?php

namespace LindenCMS\Core;

abstract class Context
{
    public function __construct(protected Node $node)
    {
    }

    // abstract public function __invoke(mixed $data = null);
}