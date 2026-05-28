<?php

namespace LindenCMS\Core;

use LindenCMS\Core\Traits\HasCollection;

class NodeCollection extends Node implements /* \ArrayAccess,  */ \Countable, \IteratorAggregate
{
    use HasCollection;

    /**
     * Make parentless typed Item
     * Return same instance on every call
     */
    public function getType(): Node
    {
        if (empty($this->type)) {
            $this->type = ($this->_collection()->type)::make();
        }

        return $this->type;
    }

    /**
     * Make parentless typed Item as a child of $this->parent
     * Return new instances on every call
     */
    public function getPrototype(): Node
    {
        $prototype = get_class($this->getType())::make();
        // if ($parent = $this->getParent()) {
        //     $prototype->setParent($parent, $this->getParentPropertyName());
        // }

        return $prototype;
    }

    /**
     * Make typed Item with adding to the collection
     * Return new instances on every call
     */
    public function makeItem(?string $uid = null)
    {
        $item = $this->getPrototype();
        if ($uid) {
            $item->setUid($uid);
        }

        $this->add($item);

        return $item;
    }

    // public function toArray(array $exclude = []): mixed
    // {
    //     $data = [];
    //     foreach ($this as $item) {
    //         $data[] = $item->toArray();
    //     }
    //     return $data;
    // }

    protected function matchUid($key)
    {
        return preg_match('/uid[a-f0-9]+/i', $key, $matches) 
            ? $matches[0] 
            : null;
    }

    public function fill(mixed $data): static
    {
        foreach ($data as $key => $nodeData) {
            $item = $this->makeItem($this->matchUid($key));
            $item->fill($nodeData);
        }

        return $this;
    }

    public function structPath(string $path, bool $isAbsolute = false): ?Node
    {
        $trace = str($path)->explode('.');
        $prototype = $this->getPrototype();
        $next = $trace->shift();
        $node = $prototype->children[$next] ?? null;
        $node?->setParent($this, $node->getParentPropertyName());
        if ($trace->isNotEmpty()) {
            return $node?->structPath($trace->implode('.'), $isAbsolute);
        }

        return $node;
    }
}
