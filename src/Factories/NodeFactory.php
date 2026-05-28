<?php

namespace LindenCMS\Core\Factories;

use LindenCMS\Core\Contracts\InitContract;
use LindenCMS\Core\Node;

class NodeFactory
{
    public function __construct(
        private InitContract $initService
    ) {}

    public function create(string $className, mixed $parent = null, string $parentPropertyName = ''): Node
    {
        /**
         * @var Node
         */
        $instance = new $className;
        $instance->setParent($parent, $parentPropertyName);
        $this->initService->init($instance);

        // Deferred Init:
        // $instance->recursionWalk(function ($item) use ($instance) {
        //     if ($item != $instance && $item->isDeferredInit()) {
        //         $this->initService->init($item);
        //         return BaseComponent::RECURSION_WALK_STATE_BREAK; // <-------- stop the propogation of nested entities
        //     }
        // });

        // // Hooks Call:
        // $instance->recursionWalk(function ($item) {
        //     $item->afterInit();
        // });

        return $instance;
    }
}
