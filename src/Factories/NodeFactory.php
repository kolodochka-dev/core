<?php

namespace LindenCMS\Core\Factories;

use LindenCMS\Core\Node;
use LindenCMS\Core\Services\RecursiveInit;
use LindenCMS\Core\Services\CachedInit;

// TODO: cache disabling from config in CMS layer 
// TODO: cache revalidation command 
class NodeFactory
{
    public function __construct(
        private RecursiveInit $recursiveInitService,
        private CachedInit $cachedInitService,
    ) {
    }

    public function create(string $className, mixed $parent = null, string $parentPropertyName = ''): Node
    {
        /**
         * @var Node
         */
        $instance = new $className;
        $instance->setParent($parent, $parentPropertyName);

        if ($this->cachedInitService->isCacheExists($instance)) {
            $this->cachedInitService->createCache($instance);
        }
        
        if (false) {
            $this->cachedInitService->init($instance, $this->cachedInitService->getCache($instance));
        } else {
            $this->recursiveInitService->init($instance);
        }

        return $instance;
    }
}
