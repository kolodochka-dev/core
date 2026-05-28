<?php

namespace LindenCMS\Core\Traits;

use LindenCMS\Core\Node;

trait HasTreeStructure
{
    protected ?Node $parent = null;
    protected string $parentPropertyName = '';
    protected string $path = '';
    protected array $children = [];

    public function getChildren(): array
    {
        foreach (array_keys($this->magicProps) as $propName) {
            $this->callMagicProp($propName);
        }

        return $this->children;
    }

    public function getChild(string $name): ?Node
    {
        return $this->getChildren()[$name] ?: null;
    }

    public function hasChild($name)
    {
        return isset($this->getChildren()[$name]);
    }

    public function setChildren(array $children, bool $autoLoadProperties = false)
    {
        foreach ($children as $key => $child) {
            $this->addChild($child, $key, $autoLoadProperties);
        }

        return $this->children = $children;
    }

    public function setParent(?Node $parent, string $parentPropertyName = '', bool $autoLoadProperties = false): void
    {
        if (!$parent) {
            $this->parent = $parent;
            $this->parentPropertyName = $parentPropertyName;
            return;
        }

        $parent->addChild($this, $parentPropertyName, $autoLoadProperties);
    }

    public function addChild(Node $child, string $parentPropertyName = '', bool $autoLoadProperties = false)
    {
        $child->parent = $this;
        $child->setParentPropertyName($parentPropertyName);
        $this->children[$parentPropertyName] = $child;

        if ($autoLoadProperties) {
            $this->{$parentPropertyName} = $child;
        }
    }

    public function getPathName()
    {
        return $this->getParentPropertyName() ?: $this->toSnakeCase();
    }

    protected function generatePath()
    {
        if (!$this->getParents()) {
            return '';
        }

        return implode('.', array_map(
            fn(Node $item) => $item->getPathName(),
            [...$this->getParents(), $this]
        ));
    }

    public function getPath(): string
    {
        return $this->generatePath();
    }

    protected function setPath(string $path)
    {
        $this->path = $path;
    }

    public function getParent(): ?Node
    {
        return $this->parent;
    }

    public function getRoot(string $class = ''): ?Node
    {
        if (!$class) {
            return $this->getParents()[0] ?? null;
        }

        foreach ($this->getParents() as $parent) {
            if ($parent instanceof $class) {
                /** @var Node $parent */
                return $parent;
            }
        }

        return null;
    }

    public function getParents(): array
    {
        $parents = $this->buildParentsTray();
        array_shift($parents);

        return array_reverse($parents);
    }

    protected function buildParentsTray(): array
    {
        if (!$this->parent) {
            return [$this];
        }

        return array_merge([$this], $this->parent->buildParentsTray());
    }

    public function parent(string $class = '')
    {
        if (!$class) {
            return $this->getParents()[0] ?? $this;
        }

        foreach (array_reverse($this->getParents()) as $parent) {
            if ($parent instanceof $class) {
                return $parent;
            }
        }

        return null;
    }

    public function getParentPropertyName(): string
    {
        return $this->parentPropertyName;
    }

    public function setParentPropertyName(string $name): void
    {
        $this->parentPropertyName = $name;
    }

    public function path(string $path, bool $isAbsolute = false): ?node
    {
        $component = $this;
        foreach (explode('.', $path) as $propertyName) {
            if ($isAbsolute && $propertyName == $this->toSnakeCase()) {
                continue;
            }

            if (!isset($component->children[$propertyName])) {
                return null;
            }

            $component = $component->{$propertyName};
        }

        return $component;
    }

    public function structPath(string $path, bool $isAbsolute = false): ?node
    {
        $trace = str($path)->explode('.');
        if ($isAbsolute) {
            // first is always the root
            $trace->shift();
            $isAbsolute = false;
        }

        $next = $trace->shift();
        $node = $this->getChildren()[$next] ?? null;

        if ($trace->isNotEmpty()) {
            return $node?->structPath($trace->implode('.'), $isAbsolute);
        }

        return $node;
    }

    public function traceTo(string $to): array 
    {
        $trace = [$this];
        $path = '';
        foreach (explode('.', $to) as $item) {
            $trace[] = $this->path("$path$item");
            $path .= "$item.";
        }

        return $trace;
    }

    public function structTraceTo(string $to): array
    {
        // $trace = [$this];
        $trace = [];
        // $path = '';
        $next = $this;
        foreach (explode('.', $to) as $item) {
            $next = $next->structPath($item);
            $trace[] = $next;
            // $path .= "$item.";
        }

        return $trace;
    }
}
