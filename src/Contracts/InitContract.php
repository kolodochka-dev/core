<?php

namespace LindenCMS\Core\Contracts;

use LindenCMS\Core\Node;

interface InitContract
{
    public function init(Node $node): void;
}