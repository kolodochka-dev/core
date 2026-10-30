<?php

namespace LindenCMS\Core;

abstract class ComplexContext extends Context
{
    public function __invoke(mixed $data = null): mixed
    {
        return $this;
    }
}
