<?php

namespace LindenCMS\Core;

class Context
{
    public function __construct(protected Node $node)
    {
    }
}