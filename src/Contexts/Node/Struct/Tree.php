<?php

namespace LindenCMS\Core\Contexts\Node\Struct;

use LindenCMS\Core\Context;
use LindenCMS\Core\NodeValue;

class Tree extends Context
{
    public function __invoke(mixed $data = null)
    {
        $tree = [];
        foreach ($this->node->getChildren() as $key => $item) {
            if (!NodeValue::matchType($item)) {
                $tree[] = $item->getPath();
                if ($itemTree = $item->ctx()->struct_tree()) {
                    $tree = array_merge($tree, $itemTree);
                }
            }
        }

        return $tree;
    }
}