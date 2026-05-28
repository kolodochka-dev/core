<?php

namespace LindenCMS\Core\Contexts;

abstract class ComplexContext extends Context
{
    public function __invoke(): mixed
    {
        return $this;
    }
}
