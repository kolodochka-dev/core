<?php

namespace LindenCMS\Core\Contexts\NodeCollection\Struct;

use LindenCMS\Core\Context;
use LindenCMS\Core\NodeCollection;

/**
 * Summary of Tree
 * 
 * @property NodeCollection $mode
 */
class Tree extends Context
{
    public function __invoke(mixed $data = null)
    {
        $prototype = $this->node->makePrototype(true);
        return $prototype->ctx()->struct_tree();
    }
}