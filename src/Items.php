<?php

namespace LindenCMS\Core;

class Items extends Node 
{
    public function fill(mixed $data): static
    {
        /** @var NodeCollection */
        $parent = $this->getParent();
        foreach ($data as $nodeData) {
            $item = $parent->makeItem();
            $item->fill($nodeData);
        }

        return $this;
    }
}
