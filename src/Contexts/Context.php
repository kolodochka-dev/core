<?php

namespace LindenCMS\Core\Contexts;

use LindenCMS\Core\Node;

abstract class Context
{
    protected Node $node;
    protected array $data = [];
    protected string $contextName;

    public function setNode(Node $node): void
    {
        $this->node = $node;
    }

    public function setData(array $data)
    {
        $this->data = $data;
    }

    public function getData(?string $key = null, mixed $default = null): mixed
    {
        if (!$key) {
            return $this->data;
        }

        if (!isset($this->data[$key])) {
            return $default;
        }

        return $this->data[$key];
    }

    public function setContextName(string $name)
    {
        $this->contextName = $name;
    }

    public function after(mixed $result): mixed
    {
        return $result;
    }

    abstract public function __invoke(): mixed;

    protected function filterRelated(?\Closure $filter = null): array
    {
        return $this->node->props(
            $filter
            ? fn($item) => $item->hasContext($this->contextName) && ($filter($item) !== false)
            : fn($item) => $item->hasContext($this->contextName)
        );
    }

    public function __get(string $name): mixed
    {
        return $this->getData($name, null);
    }

    public function __set(string $name, mixed $value): void
    {
        $this->data[$name] = $value;
    }

    public function __isset(string $name): bool
    {
        return isset($this->data[$name]);
    }
}
