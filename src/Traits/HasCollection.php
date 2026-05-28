<?php

namespace LindenCMS\Core\Traits;

use LindenCMS\Core\Node;

trait HasCollection
{
    // protected array $items = [];
    // public Items $items;
    protected int $total = 0;
    protected $type;

    // public function getTotal(): int
    // {
    //     return $this->total;
    // }

    public function getItems(): array
    {
        return $this->getChildren();
    }

    protected function validateItem(mixed $value): void
    {
        if (!$value instanceof ($this->getType())) {
            throw new \InvalidArgumentException(sprintf(
                'Collection only accepts items of type %s, %s given',
                $this->type::class,
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }
    }

    // // ArrayAccess methods
    // public function offsetExists(mixed $offset): bool
    // {
    //     return isset($this->getItems()[$offset]);
    // }

    // public function offsetGet(mixed $offset): mixed
    // {
    //     return $this->getChild($offset);
    // }

    // public function offsetSet(mixed $offset, mixed $value): void
    // {
    //     $this->validateItem($value);

    //     if ($offset === null) {
    //         $this->items[] = $value;
    //     } else {
    //         $this->items[$offset] = $value;
    //     }
    // }

    // public function offsetUnset(mixed $offset): void
    // {
    //     unset($this->items[$offset]);
    // }

    // Countable method
    public function count(): int
    {
        return count($this->getItems());
    }

    // public function remove(int $index): void
    // {
    //     unset($this->items[$index]);
    //     $this->items = array_values($this->items); // Re-index
    // }

    // public function fromArray(array $values)
    // {
    //     foreach ($values as $value) {
    //         $this->add($value);
    //     }

    //     return $this;
    // }

    // IteratorAggregate method
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->getItems());
    }

    // Custom
    public function add(Node $item): void
    {
        $this->addChild($item, $item->getUid());
        // $this[] = $item;
    }

    public function pop(): Node
    {
        $items = $this->getChildren();
        $poped = array_pop($items);
        $this->setChildren($items);
        
        return $poped;
    }

    public function shift()
    {
        $items = $this->getChildren();
        $shifted = array_shift($items);
        $this->setChildren($items);
        
        return $shifted;
    }

    public function reset()
    {
        $this->setChildren([]);
    }

    public function first()
    {
        $items = $this->getChildren();
        return reset($items);
    }

    public function setType(Node $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): Node
    {
        return $this->type;
    }
}
